<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class PaymobGateway implements PaymentGatewayInterface
{
    public function verify(string $paymentId, Order $order): array
    {
        $baseUrl = rtrim(
            config('services.paymob.base_url'),
            '/'
        );

        $auth = Http::acceptJson()
            ->timeout(15)
            ->post($baseUrl . '/api/auth/tokens', [
                'api_key' => config('services.paymob.api_key'),
            ]);

        if (! $auth->successful() || ! $auth->json('token')) {
            throw new RuntimeException(
                'Paymob authentication failed.'
            );
        }

        $response = Http::withToken($auth->json('token'))
            ->acceptJson()
            ->timeout(15)
            ->get(
                $baseUrl .
                '/api/acceptance/transactions/' .
                urlencode($paymentId)
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'Unable to verify Paymob transaction.'
            );
        }

        $data = $response->json();

        $success = ($data['success'] ?? false) === true;
        $pending = ($data['pending'] ?? false) === true;

        $status = $success
            ? 'paid'
            : ($pending ? 'pending' : 'failed');

        return [
            'status' => $status,

            'payment_id' => (string) ($data['id'] ?? $paymentId),

            'invoice_id' => (string) (
                $data['order']['id'] ?? ''
            ),

            // Paymob amount is returned in minor currency units.
            'amount' => isset($data['amount_cents'])
                ? $this->convertAmount(
                    $data['amount_cents'],
                    $data['currency'] ?? ''
                )
                : null,

            'currency' => $data['currency'] ?? null,

            // Must match the reference sent when the transaction
            // was created, not merely a frontend-supplied value.
            'reference' => $data['order']['merchant_order_id'] ?? null,

            'raw' => $data,
        ];
    }

    private function convertAmount($amount, string $currency): string
    {
        $decimals = in_array(
            strtoupper($currency),
            ['KWD', 'BHD', 'OMR'],
            true
        ) ? 3 : 2;

        return number_format(
            ((int) $amount) / (10 ** $decimals),
            $decimals,
            '.',
            ''
        );
    }
}

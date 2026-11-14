<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class MyFatoorahGateway implements PaymentGatewayInterface
{
    public function verify(string $paymentId, Order $order): array
    {
        $response = Http::withToken(
            config('services.myfatoorah.token')
        )
            ->acceptJson()
            ->timeout(15)
            ->get(
                rtrim(config('services.myfatoorah.base_url'), '/') .
                '/v3/payments/' . urlencode($paymentId)
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'Unable to verify MyFatoorah payment.'
            );
        }

        $data = $response->json('Data');

        if (! $response->json('IsSuccess') || ! $data) {
            throw new RuntimeException(
                'Invalid MyFatoorah verification response.'
            );
        }

        $invoice = $data['Invoice'] ?? [];
        $transaction = $data['Transaction'] ?? [];
        $amount = $data['Amount'] ?? [];

        $status = strtolower(
            $invoice['Status'] ?? 'pending'
        );

        $transactionStatus = strtoupper(
            $transaction['Status'] ?? ''
        );

        $paid = $status === 'paid'
            && $transactionStatus === 'SUCCESS';

        $failed = in_array(
            $transactionStatus,
            ['FAILED', 'CANCELED'],
            true
        );

        return [
            'status' => $paid
                ? 'paid'
                : ($failed ? 'failed' : 'pending'),

            'payment_id' => (string) (
                $transaction['PaymentId'] ?? $paymentId
            ),

            'invoice_id' => (string) (
                $invoice['Id'] ?? ''
            ),

            'amount' => $amount['ValueInPayCurrency'] ?? null,

            'currency' => $amount['PayCurrency'] ?? null,

            'reference' => $invoice['Reference'] ?? null,

            'raw' => $data,
        ];
    }
}

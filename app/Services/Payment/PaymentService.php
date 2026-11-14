<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\User;
use App\Models\PaymentAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected PaymentGatewayResolver $gatewayResolver
    ) {}

    public function verifyPayment(
        User $user,
        string $orderNumber,
        array $data
    ): Order {
        $order = Order::where('user_id', $user->id)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->payment_method !== Order::PAYMENT_ONLINE) {
            throw ValidationException::withMessages([
                'payment_method' => 'This order does not support online payment.',
            ]);
        }

        if ($order->status === Order::STATUS_CANCELLED) {
            throw ValidationException::withMessages([
                'order' => 'Cancelled orders cannot receive payments.',
            ]);
        }

        $gateway = strtolower($data['gateway']);

        if ($gateway !== strtolower($order->payment_gateway)) {
            throw ValidationException::withMessages([
                'gateway' => 'Payment gateway does not match this order.',
            ]);
        }

        $paymentId = $data['payment_id'];
        $key = $data['idempotency_key'];

        $attempt = PaymentAttempt::firstOrCreate(
            [
                'idempotency_key' => $key,
            ],
            [
                'order_id' => $order->id,
                'gateway' => $gateway,
                'payment_id' => $paymentId,
                'amount' => $order->total,
                'currency' => $order->currency
                    ?? config('services.payments.currency', 'KWD'),
                'status' => 'pending',
            ]
        );

        // Same key must always represent the same request.
        if (
            $attempt->order_id != $order->id ||
            $attempt->gateway !== $gateway ||
            $attempt->payment_id !== $paymentId
        ) {
            throw ValidationException::withMessages([
                'idempotency_key' =>
                    'This key was already used with different payment data.',
            ]);
        }

        // Return immediately if this attempt was already verified.
        if ($attempt->status === 'paid') {
            return $order->fresh();
        }

        // Do not allow a transaction to be reused by another attempt.
        $usedPayment = PaymentAttempt::where('gateway', $gateway)
            ->where('payment_id', $paymentId)
            ->where('id', '!=', $attempt->id)
            ->exists();

        if ($usedPayment) {
            throw ValidationException::withMessages([
                'payment_id' => 'This payment transaction has already been used.',
            ]);
        }

        // External API call is deliberately outside the DB transaction.
        $gatewayDriver = $this->gatewayResolver->resolve($gateway);

        $verified = $gatewayDriver->verify(
            $paymentId,
            $order
        );

        $this->validateGatewayResponse($verified, $order);

        return DB::transaction(function () use (
            $order,
            $attempt,
            $verified,
            $paymentId
        ) {
            $lockedOrder = Order::lockForUpdate()
                ->findOrFail($order->id);

            $lockedAttempt = PaymentAttempt::lockForUpdate()
                ->findOrFail($attempt->id);

            if ($lockedAttempt->status === 'paid') {
                return $lockedOrder;
            }

            // A transaction must not be attached to another attempt.
            $usedPayment = PaymentAttempt::where(
                'gateway',
                $lockedAttempt->gateway
            )
                ->where('payment_id', $paymentId)
                ->where('id', '!=', $lockedAttempt->id)
                ->exists();

            if ($usedPayment) {
                throw ValidationException::withMessages([
                    'payment_id' => 'Payment transaction already exists.',
                ]);
            }

            if ($verified['status'] !== 'paid') {
                $lockedAttempt->update([
                    'status' => $verified['status'],
                    'response_data' => $verified['raw'] ?? [],
                ]);

                if ($verified['status'] === 'failed') {
                    $lockedOrder->update([
                        'payment_status' => Order::PAYMENT_FAILED,
                    ]);
                }

                return $lockedOrder->fresh();
            }

            if (
                $lockedOrder->payment_status === Order::PAYMENT_PAID
            ) {
                throw ValidationException::withMessages([
                    'payment_status' => 'Order is already paid by another transaction.',
                ]);
            }

            // Existing model allows failed -> pending, then pending -> paid.
            if (
                $lockedOrder->payment_status === Order::PAYMENT_FAILED
            ) {
                $lockedOrder->update([
                    'payment_status' => Order::PAYMENT_PENDING,
                ]);
            }

            $lockedAttempt->update([
                'status' => 'paid',
                'response_data' => $verified['raw'] ?? [],
                'verified_at' => now(),
            ]);

            $lockedOrder->update([
                'payment_status' => Order::PAYMENT_PAID,
                'payment_id' => $verified['payment_id'],
                'invoice_id' => $verified['invoice_id'] ?: null,
                'payment_data' => $verified['raw'] ?? [],
                'paid_at' => $lockedOrder->paid_at ?? now(),
            ]);

            return $lockedOrder->fresh();
        });
    }

    private function validateGatewayResponse(
        array $verified,
        Order $order
    ): void {
        if (! in_array(
            $verified['status'] ?? null,
            ['paid', 'failed', 'pending'],
            true
        )) {
            throw new RuntimeException(
                'Invalid payment status returned by gateway.'
            );
        }

        // Never trust a payment response without its order reference.
        if (
            empty($verified['reference']) ||
            (string) $verified['reference'] !== (string) $order->number
        ) {
            throw ValidationException::withMessages([
                'payment_id' => 'Payment reference does not match the order.',
            ]);
        }

        if (
            empty($verified['amount']) ||
            empty($verified['currency'])
        ) {
            throw new RuntimeException(
                'Gateway response is missing amount or currency.'
            );
        }

        $expectedCurrency = strtoupper(
            $order->currency
            ?? config('services.payments.currency', 'KWD')
        );

        if (
            strtoupper($verified['currency']) !== $expectedCurrency
        ) {
            throw ValidationException::withMessages([
                'payment_id' => 'Payment currency does not match the order.',
            ]);
        }

        // This example assumes the order uses a 3-decimal KWD amount.
        // Use your project's money/decimal comparison helper if available.
        if (
            number_format((float) $verified['amount'], 3, '.', '') !==
            number_format((float) $order->total, 3, '.', '')
        ) {
            throw ValidationException::withMessages([
                'payment_id' => 'Payment amount does not match the order.',
            ]);
        }
    }
}

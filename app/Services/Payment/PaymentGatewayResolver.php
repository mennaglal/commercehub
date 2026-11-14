<?php

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentGatewayResolver
{
    public function resolve(string $gateway)
    {
        switch ($gateway) {
            case 'myfatoorah':
                return app(
                    Gateways\MyFatoorahGateway::class
                );

            case 'paymob':
                return app(
                    Gateways\PaymobGateway::class
                );

            case 'paypal':
                return app(
                    Gateways\PayPalGateway::class
                );

            default:
                throw new InvalidArgumentException(
                    'Unsupported payment gateway.'
                );
        }
    }
}

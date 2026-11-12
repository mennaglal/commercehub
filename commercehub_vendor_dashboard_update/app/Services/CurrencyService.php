<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Session;

class CurrencyService
{
    public function baseCurrency(): Currency
    {
        return Currency::query()->firstOrCreate(
            ['code' => 'EGP'],
            [
                'name_ar' => 'الجنيه المصري',
                'name_en' => 'Egyptian Pound',
                'symbol' => 'ج.م',
                'decimal_places' => 2,
                'rate_to_egp' => 1,
                'is_active' => true,
            ]
        );
    }

    public function currentCurrency(): Currency
    {
        $code = Session::get('currency_code', 'EGP');

        $currency = Currency::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->whereNotNull('rate_to_egp')
            ->first();

        if (!$currency) {
            Session::put('currency_code', 'EGP');

            return $this->baseCurrency();
        }

        return $currency;
    }

    public function convertFromEgp(float|int|string $amount): float
    {
        $currency = $this->currentCurrency();
        $rate = (float) $currency->rate_to_egp;

        if ($rate <= 0) {
            return (float) $amount;
        }

        return round(
            (float) $amount / $rate,
            (int) $currency->decimal_places
        );
    }

    public function format(float|int|string $amount): string
    {
        $currency = $this->currentCurrency();

        $convertedAmount = $this->convertFromEgp($amount);

        return number_format(
                $convertedAmount,
                (int) $currency->decimal_places,
                '.',
                ','
            ) . ' ' . $currency->symbol;
    }
}

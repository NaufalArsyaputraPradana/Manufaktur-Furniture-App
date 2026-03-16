<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencyConversionService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.exchangerate-api.com/v4/latest/';
    protected string $baseCurrency = 'IDR';

    public function __construct()
    {
        $this->apiKey = config('services.exchangerate.api_key', '');
    }

    /**
     * Convert currency
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
        return round($amount * $rate, 2);
    }

    /**
     * Get exchange rate between two currencies
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";

        return Cache::remember($cacheKey, 3600, function () use ($fromCurrency, $toCurrency) {
            try {
                $response = Http::get($this->apiUrl . $fromCurrency);

                if ($response->successful()) {
                    $rates = $response->json('rates', []);
                    return $rates[$toCurrency] ?? $this->getDummyRate($fromCurrency, $toCurrency);
                }

                return $this->getDummyRate($fromCurrency, $toCurrency);
            } catch (\Exception $e) {
                return $this->getDummyRate($fromCurrency, $toCurrency);
            }
        });
    }

    /**
     * Get all exchange rates for base currency
     *
     * @param string $baseCurrency
     * @return array
     */
    public function getAllRates(string $baseCurrency = 'IDR'): array
    {
        $cacheKey = "all_rates_{$baseCurrency}";

        return Cache::remember($cacheKey, 3600, function () use ($baseCurrency) {
            try {
                $response = Http::get($this->apiUrl . $baseCurrency);

                if ($response->successful()) {
                    return $response->json('rates', $this->getDummyRates($baseCurrency));
                }

                return $this->getDummyRates($baseCurrency);
            } catch (\Exception $e) {
                return $this->getDummyRates($baseCurrency);
            }
        });
    }

    /**
     * Format currency with symbol
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public function format(float $amount, string $currency): string
    {
        $symbols = [
            'IDR' => 'Rp ',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'SGD' => 'S$',
            'MYR' => 'RM',
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';
        $decimals = $currency === 'JPY' ? 0 : 2;

        if ($currency === 'IDR') {
            return $symbol . number_format($amount, 0, ',', '.');
        }

        return $symbol . number_format($amount, $decimals, '.', ',');
    }

    /**
     * Get supported currencies
     *
     * @return array
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'IDR' => 'Indonesian Rupiah',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'CNY' => 'Chinese Yuan',
            'SGD' => 'Singapore Dollar',
            'MYR' => 'Malaysian Ringgit',
            'AUD' => 'Australian Dollar',
            'THB' => 'Thai Baht',
        ];
    }

    /**
     * Get dummy exchange rate for simulation
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    protected function getDummyRate(string $fromCurrency, string $toCurrency): float
    {
        // Dummy rates (approximate as of 2024)
        $rates = [
            'IDR' => [
                'USD' => 0.000063,
                'EUR' => 0.000058,
                'GBP' => 0.000050,
                'JPY' => 0.0094,
                'CNY' => 0.00046,
                'SGD' => 0.000085,
                'MYR' => 0.00030,
            ],
            'USD' => [
                'IDR' => 15800,
                'EUR' => 0.92,
                'GBP' => 0.79,
                'JPY' => 149.50,
                'CNY' => 7.28,
            ],
        ];

        if (isset($rates[$fromCurrency][$toCurrency])) {
            return $rates[$fromCurrency][$toCurrency];
        }

        // If reverse rate exists
        if (isset($rates[$toCurrency][$fromCurrency])) {
            return 1 / $rates[$toCurrency][$fromCurrency];
        }

        return 1.0;
    }

    /**
     * Get dummy all rates for simulation
     *
     * @param string $baseCurrency
     * @return array
     */
    protected function getDummyRates(string $baseCurrency): array
    {
        if ($baseCurrency === 'IDR') {
            return [
                'USD' => 0.000063,
                'EUR' => 0.000058,
                'GBP' => 0.000050,
                'JPY' => 0.0094,
                'CNY' => 0.00046,
                'SGD' => 0.000085,
                'MYR' => 0.00030,
                'AUD' => 0.000096,
                'THB' => 0.0023,
            ];
        }

        return [];
    }
}

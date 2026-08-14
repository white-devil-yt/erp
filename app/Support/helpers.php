<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('currency')) {
    function currency(float $amount): string
    {
        return setting('currency_symbol', '₹').' '.number_format($amount, 2);
    }
}

if (! function_exists('nextDocumentNumber')) {
    function nextDocumentNumber(string $prefixSetting, string $model, string $field): string
    {
        $prefix = setting($prefixSetting, 'INV-');
        $last = $model::query()->max($field);
        $lastNumber = $last ? (int) preg_replace('/[^0-9]/', '', $last) : 0;

        return $prefix.str_pad((string) max($lastNumber + 1, 1001), 5, '0', STR_PAD_LEFT);
    }
}

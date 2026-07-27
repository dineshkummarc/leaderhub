<?php

use Leaderhub\Core\Models\Setting;

if (! function_exists('format_price')) {
    /**
     * Format price based on admin setting (usd/idr/hidden).
     * Returns null if hidden, formatted string otherwise.
     */
    function format_price(?float $price, ?string $override = null): ?string
    {
        if ($price === null) {
            return null;
        }

        $mode = $override ?? Setting::get('price_display', 'general', 'usd');

        return match ($mode) {
            'hidden' => null,
            'idr' => 'Rp '.number_format($price * 16000, 0, ',', '.'),
            default => '$'.number_format($price, 0),
        };
    }
}

if (! function_exists('price_display_mode')) {
    /**
     * Get the current price display mode.
     */
    function price_display_mode(): string
    {
        return Setting::get('price_display', 'general', 'usd');
    }
}

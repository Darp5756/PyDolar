<?php

namespace Darp5756\PyDolar;

use Darp5756\PyDolar\Enums\Currencies;
use Darp5756\PyDolar\Enums\FormatDates;
use Darp5756\PyDolar\Enums\Pages;
use Exception;
use Illuminate\Support\Facades\Http;

class PyDolar {
    private const URL_API = 'https://pydolarve.org/api/v1/';

    public static function getData (Currencies $currency, Pages $page, string $monitor, FormatDates $formatDate, bool $roundedPrice): array {
        if (!self::isMonitorValid($currency, $page, $monitor)) {
            throw new Exception('Monitor is invalid');
        }
        return Http::get(self::URL_API . $currency->value, [
            'page' => $page->value,
            'monitor' => $monitor,
            'format_date' => $formatDate->value,
            'rounded_price' => $roundedPrice,
        ])->json();
    }

    public static function getMonitors (Currencies $currency, Pages $page): array {
        if ($currency == Currencies::euro && $page != Pages::criptodolar) {
            return [];
        }
        $monitors = json_decode(file_get_contents(__DIR__ .'/json/monitors.json'), true);
        return $monitors[$page->value];
    }

    public static function isMonitorValid (Currencies $currency, Pages $page, string $monitor): bool {
        return in_array($monitor, self::getMonitors($currency, $page));
    }
}

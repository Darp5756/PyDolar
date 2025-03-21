<?php

namespace Darp5756\PyDolar;

use Darp5756\PyDolar\Enums\Currencies;
use Darp5756\PyDolar\Enums\FormatDates;
use Darp5756\PyDolar\Enums\Pages;
use Exception;
use GuzzleHttp\Client;

class PyDolar {
    private const URL_API = 'https://pydolarve.org/api/v1/';

    public static function getData (Currencies $currency, Pages $page, string $monitor, FormatDates $formatDate, bool $roundedPrice): array {
        if (!self::isMonitorValid($currency, $page, $monitor)) {
            throw new Exception('Monitor is invalid');
        }
        $client = new Client();
        $response = $client->get(self::URL_API . $currency->value, [
            'query' => [
                'page' => $page->value,
                'monitor' => $monitor,
                'format_date' => $formatDate->value,
                'rounded_price' => $roundedPrice,
            ],
        ]);
        return json_decode($response->getBody(), true);
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

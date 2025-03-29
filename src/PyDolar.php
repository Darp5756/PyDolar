<?php

namespace Darp5756\PyDolar;

use Carbon\Carbon;
use Darp5756\PyDolar\Enums\Currencies;
use Darp5756\PyDolar\Enums\FormatDates;
use Darp5756\PyDolar\Enums\Orders;
use Darp5756\PyDolar\Enums\Pages;
use Darp5756\PyDolar\Enums\RoundedPrices;
use Exception;
use GuzzleHttp\Client;

class PyDolar {
    private const URL_API = 'https://pydolarve.org/api/v1/';

    public static function getDataMonitor (
        Currencies $currency,
        Pages $page,
        string $monitor,
        FormatDates $formatDate,
        RoundedPrices $roundedPrice,
    ): array {
        self::validateMonitor($currency, $page, $monitor);
        return self::getData(
            self::URL_API . $currency->value,
            [
                'page' => $page->value,
                'monitor' => $monitor,
                'format_date' => $formatDate->value,
                'rounded_price' => $roundedPrice->value,
            ],
            false,
        );
    }

    public static function getDataHistorial(
        Currencies $currency,
        Pages $page,
        string $monitor,
        Carbon $startDate,
        Carbon $endDate,
        FormatDates $formatDate,
        RoundedPrices $roundedPrice,
        Orders $order,
    ): array {
        self::validateMonitor($currency, $page, $monitor);
        return self::getData(
            self::URL_API . $currency->value . '/history',
            [
                'page'=> $page->value,
                'monitor' => $monitor,
                'start_date' => $startDate->format('d-m-Y'),
                'end_date' => $endDate->format('d-m-Y'),
                'format_date' => $formatDate->value,
                'rounded_price' => $roundedPrice->value,
                'order' => $order->value,
            ],
            true,
        );
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

    private static function getData (
        string $uri,
        array $query,
        bool $includeAuthorization,
    ): array {
        $headers = [
            'content-type' => 'application/json',
        ];
        if ($includeAuthorization) {
            $headers['Authorization'] = 'Bearer ' . env('PYDOLAR_TOKEN');
        }
        $client = new Client();
        $response = $client->get($uri, [
            'headers' => $headers,
            'query' => $query,
        ]);
        return json_decode($response->getBody(), true);
    }

    private static function validateMonitor (
        Currencies $currency,
        Pages $page,
        string $monitor
    ): void {
        if (!self::isMonitorValid($currency, $page, $monitor)) {
            throw new Exception('Monitor is invalid');
        }
    }
}

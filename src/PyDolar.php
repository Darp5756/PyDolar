<?php

namespace Darp5756\PyDolar;

use Carbon\Carbon;
use Darp5756\PyDolar\Enums\Currencies;
use Darp5756\PyDolar\Enums\FormatDates;
use Darp5756\PyDolar\Enums\Orders;
use Darp5756\PyDolar\Enums\Pages;
use Darp5756\PyDolar\Enums\RoundedPrices;
use Darp5756\PyDolar\Enums\Types;
use Darp5756\PyDolar\Responses\CambiosResponse;
use Darp5756\PyDolar\Responses\ErrorResponse;
use Darp5756\PyDolar\Responses\HistorialResponse;
use Darp5756\PyDolar\Responses\MonitorResponse;
use Darp5756\PyDolar\Responses\MonitorsResponse;
use Darp5756\PyDolar\Responses\ValorResponse;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class PyDolar {
    private const URL_API = 'https://pydolarve.org/api/v1/';

    public static function getDataMonitor (
        Currencies $currency,
        Pages $page = Pages::alcambio,
        string $monitor = '',
        FormatDates $formatDate = FormatDates::default,
        RoundedPrices $roundedPrice = RoundedPrices::true,
    ): MonitorResponse|MonitorsResponse|ErrorResponse {
        self::validateMonitor($currency, $page, $monitor);
        $response = self::getData(
            self::URL_API . $currency->value,
            [
                'page' => $page->value,
                'monitor' => $monitor,
                'format_date' => $formatDate->value,
                'rounded_price' => $roundedPrice->value,
            ],
            false,
        );
        if ($response instanceof ErrorResponse) {
            return $response;
        }
        if (empty($monitor)) {
            return new MonitorsResponse($response->getStatusCode(), json_decode($response->getBody(), true));
        }
        return new MonitorResponse($response->getStatusCode(), json_decode($response->getBody(), true));
    }

    public static function getDataHistorial(
        Currencies $currency,
        Pages $page,
        string $monitor,
        Carbon $startDate,
        Carbon $endDate,
        FormatDates $formatDate = FormatDates::default,
        RoundedPrices $roundedPrice = RoundedPrices::true,
        Orders $order = Orders::desc,
    ): HistorialResponse|ErrorResponse {
        self::validateMonitor($currency, $page, $monitor);
        $response = self::getData(
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
        if ($response instanceof ErrorResponse) {
            return $response;
        }
        return new HistorialResponse($response->getStatusCode(), json_decode($response->getBody(), true));
    }

	public static function getDataCambios(
        Currencies $currency,
        Pages $page,
        string $monitor,
        Carbon $date,
        FormatDates $formatDate = FormatDates::default,
        RoundedPrices $roundedPrice = RoundedPrices::true,
        Orders $order = Orders::desc,
    ): CambiosResponse|ErrorResponse {
        self::validateMonitor($currency, $page, $monitor);
        $response = self::getData(
            self::URL_API . $currency->value . '/changes',
            [
                'page'=> $page->value,
                'monitor' => $monitor,
                'date' => $date->format('d-m-Y'),
                'format_date' => $formatDate->value,
                'rounded_price' => $roundedPrice->value,
                'order' => $order->value,
            ],
            true,
        );
        if ($response instanceof ErrorResponse) {
            return $response;
        }
        return new CambiosResponse($response->getStatusCode(), json_decode($response->getBody(), true));
    }

	public static function getDataValor(
		Currencies $currency,
		Types $type,
		float $value,
		Pages $page,
		string $monitor,
	): ValorResponse|ErrorResponse{
		self::validateMonitor($currency, $page, $monitor);
        $response = self::getData(
            self::URL_API . $currency->value . '/conversion',
            [
				'type' => $type->value,
				'value' => $value,
                'page'=> $page->value,
                'monitor' => $monitor,
            ],
            false,
        );
        if ($response instanceof ErrorResponse) {
            return $response;
        }
		return new ValorResponse($response->getStatusCode(), json_decode($response->getBody(), true));
	}

    public static function getMonitors (Currencies $currency, Pages $page): array {
        if ($currency == Currencies::euro && $page != Pages::criptodolar) {
            return [];
        }
        $monitors = json_decode(file_get_contents(__DIR__ .'/../resources/json/monitors.json'), true);
        return $monitors[$page->value];
    }

    public static function isMonitorValid (Currencies $currency, Pages $page, string $monitor): bool {
        return in_array($monitor, self::getMonitors($currency, $page), true);
    }

    private static function getData (
        string $uri,
        array $query,
        bool $includeAuthorization,
    ): ResponseInterface|ErrorResponse {
        try {
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
            return $response;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = (string) $e->getResponse()->getBody();
                $data = json_decode($responseBody, true);

                // Verifica si el cuerpo tiene un mensaje de error
                if (isset($data['error']) && isset($data['message'])) {
                    return new ErrorResponse($statusCode, $data['error'], $data['message']);
                }

                // Si no hay un mensaje de error claro, devuelve el cuerpo tal cual
                return new ErrorResponse($statusCode, 'Unknown error', $responseBody);
            }

            // Si no hay respuesta del servidor
            return new ErrorResponse(0, 'No response', $e->getMessage());
        }
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

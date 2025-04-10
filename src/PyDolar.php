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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

/**
 * Esta clase proporciona métodos para interactuar con la API de PyDolarVe.
 * Permite obtener información sobre el tipo de cambio del dólar y otras monedas,
 * así como realizar conversiones y obtener datos históricos.
 */
class PyDolar
{
	/**
	 * URL base de la API de PyDolarVe.
	 */
    private const URL_PYDOLAR = 'https://pydolarve.org/api/v1/';

	/**
	 * Ruta al archivo JSON que contiene la información de los monitores.
	 */
	private const JSON_MONITORS = __DIR__ .'/../resources/json/monitors.json';

	/**
	 * Caché interna de los monitores cargados desde el archivo JSON.
	 * Se inicializa al primer acceso para evitar múltiples lecturas del archivo.
	 * Puede ser null si aún no se ha cargado.
	 */
	private static ?array $monitors = null;

	/**
	 * Obtiene los monitores disponibles según la moneda y la página.
	 * Si la moneda es euro y la página no es "criptodólar", se retorna un array vacío.
	 * Los datos se cargan desde un archivo JSON y se almacenan en caché estática
	 * para evitar múltiples lecturas del archivo durante la ejecución.
	 *
	 * @param Currencies $currency Moneda seleccionada.
	 * @param Pages $page Página para la que se desean los monitores.
	 * @return array Lista de monitores correspondientes a la página, o vacío si no aplica.
	 */
	public static function getMonitors (
		Currencies $currency,
		Pages $page
	): array
	{
        if ($currency == Currencies::euro && $page != Pages::criptodolar) {
            return [];
        }
		if (!self::$monitors) {
        	self::$monitors = json_decode(file_get_contents(self::JSON_MONITORS), true);
		}
        return self::$monitors[$page->value] ?? [];
    }

	/**
	 * Verifica si un monitor es válido para una combinación de moneda y página.
	 * Utiliza la lista de monitores correspondiente obtenida mediante `getMonitors`.
	 * Retorna false si el monitor no se encuentra en la lista o si es una cadena vacía.
	 *
	 * @param Currencies $currency Moneda seleccionada.
	 * @param Pages $page para la que se desea conocer si el monitor es válido
	 * @param string $monitor Nombre del monitor a validar.
	 * @return bool true si el monitor es válido y no es una cadena vacía; false en caso contrario.
	 */
    public static function isMonitorValid (
		Currencies $currency,
		Pages $page,
		string $monitor
	): bool
	{
        return in_array($monitor, self::getMonitors($currency, $page), true);
    }

	/**
	 * Obtiene los datos de un monitor o una lista de monitores desde la API.
	 * Esta función valida el monitor proporcionado y luego realiza una solicitud a la API para obtener
	 * los datos de los monitores o un monitor específico. Dependiendo de si el monitor está vacío o no,
	 * la respuesta se devolverá como un `MonitorsResponse` o `MonitorResponse`. Si ocurre un error durante
	 * la solicitud, se devolverá un objeto `ErrorResponse`.
	 *
	 * @param Currencies $currency Moneda para la consulta.
	 * @param Pages $page Página de la consulta (por defecto es `criptodolar`).
	 * @param string $monitor Monitor a consultar (por defecto es una cadena vacía).
	 * @param FormatDates $formatDate Formato de fecha para la consulta (por defecto es `default`).
	 * @param RoundedPrices $roundedPrice Indicador de precios redondeados (por defecto es `true`).
	 * @param ?float $timeout Tiempo máximo de espera para la solicitud (en segundos).
	 * @return MonitorResponse|MonitorsResponse|ErrorResponse Respuesta con los datos del monitor o un error.
	 */
	public static function getDataMonitor (
		Currencies $currency,
		Pages $page = Pages::criptodolar,
		string $monitor = '',
		FormatDates $formatDate = FormatDates::default,
		RoundedPrices $roundedPrice = RoundedPrices::true,
		?float $timeout = null,
	): MonitorResponse|MonitorsResponse|ErrorResponse
	{
		self::validateMonitor($currency, $page, $monitor, true);
		$response = self::getData(
			self::URL_PYDOLAR . $currency->value,
			[
				'page' => $page->value,
				'monitor' => $monitor,
				'format_date' => $formatDate->value,
				'rounded_price' => $roundedPrice->value,
			],
			false,
			$timeout,
		);
		if ($response instanceof ErrorResponse) {
			return $response;
		}
		if (empty($monitor)) {
			return new MonitorsResponse($response->getStatusCode(), json_decode($response->getBody(), true));
		}
		return new MonitorResponse($response->getStatusCode(), json_decode($response->getBody(), true));
	}

	/**
	 * Obtiene los datos históricos de un monitor para una moneda y página específicas dentro de un rango de fechas.
	 *
	 * @param Currencies $currency Moneda asociada al historial.
	 * @param Pages $page Página asociada al historial.
	 * @param string $monitor Monitor a utilizar en la consulta.
	 * @param Carbon $startDate Fecha de inicio del historial.
	 * @param Carbon $endDate Fecha de fin del historial.
	 * @param FormatDates $formatDate Formato de fecha para la respuesta (por defecto es `default`).
	 * @param RoundedPrices $roundedPrice Indica si se deben redondear los precios (por defecto es `true`).
	 * @param Orders $order Orden de los resultados (por defecto es `desc`).
	 * @param ?float $timeout Tiempo máximo de espera para la solicitud (en segundos).
	 * @return HistorialResponse|ErrorResponse Respuesta con los datos históricos o un error.
	 */
	public static function getDataHistorial(
        Currencies $currency,
        Pages $page,
        string $monitor,
        Carbon $startDate,
        Carbon $endDate,
        FormatDates $formatDate = FormatDates::default,
        RoundedPrices $roundedPrice = RoundedPrices::true,
        Orders $order = Orders::desc,
		?float $timeout = null,
    ): HistorialResponse|ErrorResponse
	{
        self::validateMonitor($currency, $page, $monitor, false);
        $response = self::getData(
            self::URL_PYDOLAR . $currency->value . '/history',
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
			$timeout,
        );
        if ($response instanceof ErrorResponse) {
            return $response;
        }
        return new HistorialResponse($response->getStatusCode(), json_decode($response->getBody(), true));
    }

	/**
	 * Obtiene los datos de cambios de un monitor para una moneda y página específicas en una fecha determinada.
	 *
	 * @param Currencies $currency Moneda asociada a los cambios.
	 * @param Pages $page Página asociada a los cambios.
	 * @param string $monitor Monitor a utilizar en la consulta.
	 * @param Carbon $date Fecha en la que se desean los cambios.
	 * @param FormatDates $formatDate Formato de fecha para la respuesta (por defecto es `default`).
	 * @param RoundedPrices $roundedPrice Indica si se deben redondear los precios (por defecto es `true`).
	 * @param Orders $order Orden de los resultados (por defecto es `desc`).
	 * @param ?float $timeout Tiempo máximo de espera para la solicitud (en segundos).
	 * @return CambiosResponse|ErrorResponse Respuesta con los datos de cambios o un error.
	 */
	public static function getDataCambios(
        Currencies $currency,
        Pages $page,
        string $monitor,
        Carbon $date,
        FormatDates $formatDate = FormatDates::default,
        RoundedPrices $roundedPrice = RoundedPrices::true,
        Orders $order = Orders::desc,
		?float $timeout = null,
    ): CambiosResponse|ErrorResponse
	{
        self::validateMonitor($currency, $page, $monitor, false);
        $response = self::getData(
            self::URL_PYDOLAR . $currency->value . '/changes',
            [
                'page'=> $page->value,
                'monitor' => $monitor,
                'date' => $date->format('d-m-Y'),
                'format_date' => $formatDate->value,
                'rounded_price' => $roundedPrice->value,
                'order' => $order->value,
            ],
            true,
			$timeout,
        );
        if ($response instanceof ErrorResponse) {
            return $response;
        }
        return new CambiosResponse($response->getStatusCode(), json_decode($response->getBody(), true));
    }

	/**
	 * Obtiene los datos de conversión para un valor específico, moneda, tipo, página y monitor.
	 *
	 * @param Currencies $currency Moneda para la conversión.
	 * @param Types $type Tipo de conversión.
	 * @param float $value Valor a convertir.
	 * @param Pages $page Página que se utilizará en la consulta.
	 * @param string $monitor Monitor a utilizar en la consulta.
	 * @param ?float $timeout Tiempo máximo de espera para la solicitud (en segundos).
	 * @return ValorResponse|ErrorResponse Respuesta con los datos de conversión o un error.
	 */
	public static function getDataValor(
		Currencies $currency,
		Types $type,
		float $value,
		Pages $page,
		string $monitor,
		?float $timeout = null,
	): ValorResponse|ErrorResponse
	{
		self::validateMonitor($currency, $page, $monitor, false);
        $response = self::getData(
            self::URL_PYDOLAR . $currency->value . '/conversion',
            [
				'type' => $type->value,
				'value' => $value,
                'page'=> $page->value,
                'monitor' => $monitor,
            ],
            false,
			$timeout,
        );
        if ($response instanceof ErrorResponse) {
            return $response;
        }
		return new ValorResponse($response->getStatusCode(), json_decode($response->getBody(), true));
	}

	/**
	 * Realiza una solicitud GET a una API y devuelve la respuesta o un error.
	 * Si la opción `$includeAuthorization` es verdadera, se añade un token de autorización en los encabezados.
	 * Si ocurre un error durante la solicitud, se captura la excepción y se devuelve un objeto `ErrorResponse`
	 * con el código de estado y el mensaje de error.
	 *
	 * @param string $uri URI a la que se enviará la solicitud GET.
	 * @param array $query Parámetros de consulta para la solicitud GET.
	 * @param bool $includeAuthorization Indica si se debe incluir el token de autorización en los encabezados.
	 * @param ?float $timeout Tiempo máximo de espera para la solicitud (en segundos).
	 * @return ResponseInterface|ErrorResponse Respuesta de la API o un objeto `ErrorResponse` en caso de error.
	 */
	private static function getData (
        string $uri,
        array $query,
        bool $includeAuthorization,
		?float $timeout = null,
    ): ResponseInterface|ErrorResponse
	{
		$timeoutUtilizar = floatval($timeout ?? env('PYDOLAR_TIMEOUT') ?? 0);
		if ($timeoutUtilizar < 0) {
			throw new InvalidArgumentException('Timeout cannot be less than 0.');
		}
        try {
            $headers = [
                'content-type' => 'application/json',
            ];
            if ($includeAuthorization) {
                $headers['Authorization'] = 'Bearer ' . env('PYDOLAR_TOKEN');
            }
			$client = new Client();
            $response = $client->get(
				$uri,
				[
					'headers' => $headers,
					'query' => $query,
					'timeout' => $timeoutUtilizar,
				]
			);
            return $response;
		} catch (ConnectException $e) {
			return new ErrorResponse(0, 'Timeout', 'The request exceeded the timeout limit.');
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

	/**
	 * Verifica si el monitor es válido para la moneda y la página especificadas.
	 * Si el monitor está vacío y se permite (según el parámetro `$emptyValid`), no lanza ninguna excepción.
	 * Si el monitor no es válido (y no está vacío o no se permite vacío), lanza una excepción `InvalidArgumentException` con un mensaje de error.
	 *
	 * @param Currencies $currency Moneda asociada al monitor.
	 * @param Pages $page Página asociada al monitor.
	 * @param string $monitor Monitor a validar.
	 * @param bool $emptyValid Si `true`, se permite que el monitor esté vacío; si `false`, se considera inválido si está vacío.
	 * @return void
	 * @throws InvalidArgumentException Si el monitor no es válido o está vacío cuando no está permitido.
	 */
	private static function validateMonitor(
		Currencies $currency,
		Pages $page,
		string $monitor,
		bool $emptyValid,
	): void
	{
		// Si el monitor está vacío y se permite, no se hace nada
		if ($emptyValid && empty($monitor)) {
			return;
		}

		// Si el monitor no está vacío y no es válido, se lanza una excepción
		if (!self::isMonitorValid($currency, $page, $monitor)) {
			throw new InvalidArgumentException('Monitor is invalid');
		}
	}
}

<?php

namespace Darp5756\PyDolar\Tests\Unit;

use Carbon\Carbon;
use Darp5756\PyDolar\Enums\Currencies;
use Darp5756\PyDolar\Enums\FormatDates;
use Darp5756\PyDolar\Enums\Orders;
use Darp5756\PyDolar\Enums\Pages;
use Darp5756\PyDolar\Enums\RoundedPrices;
use Darp5756\PyDolar\Enums\Types;
use Darp5756\PyDolar\PyDolar;
use Darp5756\PyDolar\Responses\CambiosResponse;
use Darp5756\PyDolar\Responses\HistorialResponse;
use Darp5756\PyDolar\Responses\MonitorResponse;
use Darp5756\PyDolar\Responses\MonitorsResponse;
use Darp5756\PyDolar\Responses\ValorResponse;
use Dotenv\Dotenv;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PyDolarTest extends TestCase
{
    /**
	 * Fecha actual para los tests.
	 */
	private static Carbon $date;

	/**
	 * Fecha de inicio para los tests.
	 */
	private static Carbon $startDate;

	/**
	 * Fecha de fin para los tests.
	 */
	private static Carbon $endDate;

	/**
	 * Configura las fechas necesarias antes de que se ejecute la clase de tests.
	 * Este método se ejecuta una vez antes de las pruebas para cargar las variables de entorno
	 * @return void
	 */
	public static function setUpBeforeClass(): void
	{
		// Cargar variables de entorno
		Dotenv::createImmutable(__DIR__.'/../../')->load();
		self::$date = Carbon::parse(env('PYDOLAR_TEST_DATE'));
		self::$startDate = Carbon::parse(env('PYDOLAR_TEST_START_DATE'));
		self::$endDate = Carbon::parse(env('PYDOLAR_TEST_END_DATE'));
	}

	#region Pruebas para getMonitors ($currency, $page)

	/**
	 * Verifica que `getMonitors` devuelve resultados para 'dolartoday' y dólar.
	 * @return void
	 */
	public function testMonitoresDolarDolartoday (): void
	{
		$this->assertNotEmpty(
			PyDolar::getMonitors(
				Currencies::dollar,
				Pages::dolartoday,
			)
		);
	}

	/**
	 * Verifica que `getMonitors` devuelve resultados para 'criptodolar' y dólar.
	 * @return void
	 */
	public function testMonitoresEuroCriptodolar (): void
	{
		$this->assertNotEmpty(
			PyDolar::getMonitors(
				Currencies::dollar,
				Pages::criptodolar,
			)
		);
	}

	/**
	 * Verifica que `getMonitors` retorna vacío cuando la moneda es euro y la página no es 'criptodolar'.
	 * @return void
	 */
	public function testSinMonitoresEuroNoCriptodolar (): void
	{
		$this->assertEmpty(
			PyDolar::getMonitors(
				Currencies::euro,
				Pages::dolartoday,
			)
		);
	}

	#endregion

	#region Pruebas para isMonitorValid ($currency, $page, $monitor)

	/**
	 * Verifica que `isMonitorValid` retorna `true` para un monitor válido.
	 * @return void
	 */
	public function testMonitorValido (): void
	{
		$this->assertTrue(
			PyDolar::isMonitorValid(
				Currencies::euro,
				Pages::criptodolar,
				'amazon_gift_card',
			)
		);
	}

	/**
	 * Verifica que `isMonitorValid` retorna `false` para un monitor no válido.
	 * @return void
	 */
	public function testMonitorInvalido(): void
	{
		$this->assertFalse(
			PyDolar::isMonitorValid(
				Currencies::dollar,
				Pages::dolartoday,
				'monitorInvalido',
			)
		);
	}

	/**
	 * Verifica que `isMonitorValid` retorna `false` para un monitor con cadena vacía.
	 * @return void
	 */
	public function testMonitorInvalidoCadenaVacia(): void
	{
		$this->assertFalse(
			PyDolar::isMonitorValid(
				Currencies::dollar,
				Pages::alcambio,
				'',
			)
		);
	}

	#endregion

	#region Pruebas para getDataMonitor ($currency, $page, $monitor, $formatDate, $roundedPrice)

	/**
	 * Prueba que se lance una excepción `InvalidArgumentException` cuando el monitor proporcionado es inválido.
	 * @return void
	 * @throws InvalidArgumentException Si el monitor es inválido.
	 */
	public function testExceptionMonitorInvalidoGetDataMonitor (): void
	{
		$this->expectException(InvalidArgumentException::class);
		PyDolar::getDataMonitor(
			Currencies::dollar,
			Pages::dolartoday,
			'monitorInvalido',
			FormatDates::default,
			RoundedPrices::true,
		);
	}

	/**
	 * Verifica que la respuesta de `getDataMonitor` sea una instancia de `MonitorsResponse` cuando el monitor está vacío.
	 * @return void
	 */
	public function testResponseMonitorsGetDataMonitor (): void
	{
		$this->assertInstanceOf(
			MonitorsResponse::class,
			PyDolar::getDataMonitor(
				Currencies::dollar,
				Pages::alcambio,
				'',
				FormatDates::default,
				RoundedPrices::true,
			)
		);
	}

	/**
	 * Verifica que la respuesta de `getDataMonitor` sea una instancia de `MonitorResponse` cuando se pasa un monitor específico.
	 * @return void
	 */
	public function testResponseMonitorGetDataMonitor (): void
	{
		$this->assertInstanceOf(
			MonitorResponse::class,
			PyDolar::getDataMonitor(
				Currencies::dollar,
				Pages::alcambio,
				'bcv',
				FormatDates::default,
				RoundedPrices::true,
			)
		);
	}

	#endregion

	#region Pruebas para getDataHistorial ($currency, $page, $monitor, $startDate, $endDate, $formatDate, $roundedPrice, $order)

	/**
	 * Prueba que se lance una excepción `InvalidArgumentException` cuando el monitor proporcionado es inválido.
	 * @return void
	 * @throws InvalidArgumentException Si el monitor es inválido.
	 */
	public function testExceptionMonitorInvalidoGetDataHistorial (): void
	{
		$this->expectException(InvalidArgumentException::class);
		PyDolar::getDataHistorial(
			Currencies::dollar,
			Pages::dolartoday,
			'monitorInvalido',
			self::$startDate,
			self::$endDate,
			FormatDates::default,
			RoundedPrices::true,
			Orders::desc,
		);
	}

	/**
	 * Prueba que se lance una excepción `InvalidArgumentException` cuando el monitor proporcionado está vacío.
	 * @return void
	 * @throws InvalidArgumentException Si el monitor es inválido.
	 */
	public function testExceptionMonitorVacioGetDataHistorial (): void
	{
		$this->expectException(InvalidArgumentException::class);
		PyDolar::getDataHistorial(
			Currencies::dollar,
			Pages::dolartoday,
			'',
			self::$startDate,
			self::$endDate,
			FormatDates::default,
			RoundedPrices::true,
			Orders::desc,
		);
	}

	/**
	 * Verifica que la respuesta de `getDataHistorial` sea una instancia de `HistorialResponse`.
	 * @return void
	 */
	public function testResponseHistorialGetDataHistorial (): void
	{
		$this->assertInstanceOf(
			HistorialResponse::class,
			PyDolar::getDataHistorial(
				Currencies::dollar,
				Pages::alcambio,
				'bcv',
				self::$startDate,
				self::$endDate,
				FormatDates::default,
				RoundedPrices::true,
				Orders::desc,
			)
		);
	}

	#endregion

	#region Pruebas para getDataCambios ($currency, $page, $monitor, $date, $formatDate, $roundedPrice, $order)

	/**
	 * Prueba que se lance una excepción `InvalidArgumentException` cuando el monitor proporcionado es inválido.
	 * @return void
	 * @throws InvalidArgumentException Si el monitor es inválido.
	 */
	public function testExceptionMonitorInvalidoGetDataCambios (): void
	{
		$this->expectException(InvalidArgumentException::class);
		PyDolar::getDataCambios(
			Currencies::dollar,
			Pages::dolartoday,
			'monitorInvalido',
			self::$date,
			FormatDates::default,
			RoundedPrices::true,
			Orders::desc,
		);
	}

	/**
	 * Prueba que se lance una excepción `InvalidArgumentException` cuando el monitor proporcionado está vacío.
	 * @return void
	 * @throws InvalidArgumentException Si el monitor es inválido.
	 */
	public function testExceptionMonitorVacioGetDataCambios (): void
	{
		$this->expectException(InvalidArgumentException::class);
		PyDolar::getDataCambios(
			Currencies::dollar,
			Pages::dolartoday,
			'',
			self::$date,
			FormatDates::default,
			RoundedPrices::true,
			Orders::desc,
		);
	}

	/**
	 * Verifica que la respuesta de `getDataCambios` sea una instancia de `CambiosResponse`.
	 * @return void
	 */
	public function testResponseCambiosGetDataCambios (): void
	{
		$this->assertInstanceOf(
			CambiosResponse::class,
			PyDolar::getDataCambios(
				Currencies::dollar,
				Pages::alcambio,
				'bcv',
				self::$date,
				FormatDates::default,
				RoundedPrices::true,
				Orders::desc,
			)
		);
	}

	#endregion

	#region Pruebas para getDataValor ($currency, $type, $value, $page, $monitor)

	/**
	 * Prueba que se lance una excepción `InvalidArgumentException` cuando el monitor proporcionado es inválido.
	 * @return void
	 * @throws InvalidArgumentException Si el monitor es inválido.
	 */
	public function testExceptionMonitorInvalidoGetDataValor (): void
	{
		$this->expectException(InvalidArgumentException::class);
		PyDolar::getDataValor(
			Currencies::dollar,
			Types::USD,
			1,
			Pages::dolartoday,
			'monitorInvalido',
		);
	}

	/**
	 * Prueba que se lance una excepción `InvalidArgumentException` cuando el monitor proporcionado está vacío.
	 * @return void
	 * @throws InvalidArgumentException Si el monitor es inválido.
	 */
	public function testExceptionMonitorVacioGetDataValor (): void
	{
		$this->expectException(InvalidArgumentException::class);
		PyDolar::getDataValor(
			Currencies::dollar,
			Types::USD,
			1,
			Pages::dolartoday,
			'',
		);
	}

	/**
	 * Verifica que la respuesta de `getDataValor` sea una instancia de `ValorResponse`.
	 * @return void
	 */
	public function testResponseValorGetDataValor (): void
	{
		$this->assertInstanceOf(
			ValorResponse::class,
			PyDolar::getDataValor(
				Currencies::dollar,
				Types::USD,
				1,
				Pages::alcambio,
				'bcv',
			)
		);
	}

	#endregion
}

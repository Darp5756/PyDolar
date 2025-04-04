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
use Darp5756\PyDolar\Responses\ValorResponse;
use Dotenv\Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;

class PyDolarTest extends TestCase
{
    private static Carbon $date;
    private static Carbon $startDate;
    private static Carbon $endDate;

    public static function setUpBeforeClass(): void
    {
        //Cargar variables de entorno
        Dotenv::createImmutable(__DIR__.'/../../')->load();
        self::$date = Carbon::parse(env('DATE_TEST'));
        self::$startDate = Carbon::parse(env('START_DATE_TEST'));
        self::$endDate = Carbon::parse(env('END_DATE_TEST'));
    }

    // getDataMonitor($currency, $page, $monitor, $formatDate, $roundedPrice)

    public function testExceptionMonitorInvalidoGetDataMonitor (): void
	{
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataMonitor(Currencies::dollar, Pages::dolartoday, 'monitorInvalido', FormatDates::default, RoundedPrices::true);
    }

    public function testExceptionSinMonitorGetDataMonitor (): void
	{
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataMonitor(Currencies::euro, Pages::dolartoday, 'dolartoday', FormatDates::default, RoundedPrices::true);
    }

    public function testGetDataMonitor (): void
	{
        $this->assertInstanceOf(MonitorResponse::class, PyDolar::getDataMonitor(Currencies::dollar, Pages::alcambio, 'bcv', FormatDates::default, RoundedPrices::true));
    }

    // getDataHistorial()

    public function testExceptionMonitorInvalidoGetDataHistorial (): void
	{
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataHistorial(Currencies::dollar, Pages::dolartoday, 'monitorInvalido', self::$startDate, self::$endDate, FormatDates::default, RoundedPrices::true, Orders::asc);
    }

    public function testExceptionSinMonitorGetDataHistorial (): void
	{
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataHistorial(Currencies::euro, Pages::dolartoday, 'dolartoday',   self::$startDate, self::$endDate, FormatDates::default, RoundedPrices::true, Orders::asc);
    }

    public function testGetDataHistorial (): void
	{
        $this->assertInstanceOf(HistorialResponse::class, PyDolar::getDataHistorial(Currencies::dollar, Pages::alcambio, 'bcv', self::$startDate, self::$endDate, FormatDates::default, RoundedPrices::true, Orders::asc));
    }

	// getDataCambios()

    public function testExceptionMonitorInvalidoGetDataCambios (): void
	{
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataCambios(Currencies::dollar, Pages::dolartoday, 'monitorInvalido', self::$date, FormatDates::default, RoundedPrices::true, Orders::asc);
    }

    public function testExceptionSinMonitorGetDataCambios (): void
	{
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataCambios(Currencies::euro, Pages::dolartoday, 'dolartoday',   self::$date, FormatDates::default, RoundedPrices::true, Orders::asc);
    }

    public function testGetDataCambios (): void
	{
        $this->assertInstanceOf(CambiosResponse::class, PyDolar::getDataCambios(Currencies::dollar, Pages::alcambio, 'bcv', self::$date, FormatDates::default, RoundedPrices::true, Orders::asc));
    }

	// getDataValor()

    public function testExceptionMonitorInvalidoGetDataValor (): void
	{
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataValor(Currencies::dollar, Types::USD, 1, Pages::dolartoday, 'monitorInvalido');
    }

    public function testExceptionSinMonitorGetDataValor (): void
	{
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataValor(Currencies::euro, Types::USD, 1, Pages::dolartoday, 'dolartoday', );
    }

    public function testGetDataValor (): void
	{
        $this->assertInstanceOf(ValorResponse::class, PyDolar::getDataValor(Currencies::dollar, Types::USD, 1, Pages::alcambio, 'bcv'));
    }

    // getMonitors($currency, $page)

    public function testMonitoresDolarDolartoday (): void
	{
        $this->assertNotEmpty(PyDolar::getMonitors(Currencies::dollar, Pages::dolartoday));
    }

    public function testMonitoresEuroCriptodolar (): void
	{
        $this->assertNotEmpty(PyDolar::getMonitors(Currencies::dollar, Pages::criptodolar));
    }

    public function testSinMonitoresEuroNoCriptodolar (): void
	{
        $this->assertEmpty(PyDolar::getMonitors(Currencies::euro, Pages::dolartoday));
    }

    // isMonitorValid($currency, $page, $monitor)

    public function testMonitorValido (): void
	{
        $this->assertTrue(PyDolar::isMonitorValid(Currencies::euro, Pages::criptodolar, 'amazon_gift_card'));
    }

    public function testMonitorInvalido(): void
	{
        $this->assertFalse(PyDolar::isMonitorValid(Currencies::dollar, Pages::dolartoday, 'monitorInvalido'));
    }

	public function testMonitorInvalidoCadenaVacia(): void
	{
		$this->assertFalse(PyDolar::isMonitorValid(Currencies::dollar, Pages::alcambio, ''));
	}

    // Funciones privadas

    private function testExceptionMonitorIsInvalid (): void
	{
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Monitor is invalid');
    }
}

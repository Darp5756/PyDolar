<?php

namespace Darp5756\PyDolar\Tests\Unit;

use Carbon\Carbon;
use Darp5756\PyDolar\Enums\Currencies;
use Darp5756\PyDolar\Enums\FormatDates;
use Darp5756\PyDolar\Enums\Orders;
use Darp5756\PyDolar\Enums\Pages;
use Darp5756\PyDolar\Enums\RoundedPrices;
use Darp5756\PyDolar\PyDolar;
use Dotenv\Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;

class PyDolarTest extends TestCase {

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

    public function testExceptionMonitorInvalidoGetDataMonitor () {
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataMonitor(Currencies::dollar, Pages::dolartoday, 'monitorInvalido', FormatDates::default, RoundedPrices::true);
    }

    public function testExceptionSinMonitorGetDataMonitor () {
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataMonitor(Currencies::euro, Pages::dolartoday, 'dolartoday', FormatDates::default, RoundedPrices::true);
    }

    public function testGetDataMonitor () {
        return $this->assertNotEmpty(PyDolar::getDataMonitor(Currencies::dollar, Pages::alcambio, 'bcv', FormatDates::default, RoundedPrices::true));
    }

    // getDataHistorial()

    public function testExceptionMonitorInvalidoGetDataHistorial () {
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataHistorial(Currencies::dollar, Pages::dolartoday, 'monitorInvalido', self::$startDate, self::$endDate, FormatDates::default, RoundedPrices::true, Orders::asc);
    }

    public function testExceptionSinMonitorGetDataHistorial () {
        $this->testExceptionMonitorIsInvalid();
        PyDolar::getDataHistorial(Currencies::euro, Pages::dolartoday, 'dolartoday',   self::$startDate, self::$endDate, FormatDates::default, RoundedPrices::true, Orders::asc);
    }

    public function testGetDataHistorial () {
        return $this->assertNotEmpty(PyDolar::getDataHistorial(Currencies::dollar, Pages::alcambio, 'bcv', self::$startDate, self::$endDate, FormatDates::default, RoundedPrices::true, Orders::asc));
    }

    // getMonitors($currency, $page)

    public function testMonitoresDolarDolartoday () {
        return $this->assertNotEmpty(PyDolar::getMonitors(Currencies::dollar, Pages::dolartoday));
    }

    public function testMonitoresEuroCriptodolar () {
        return $this->assertNotEmpty(PyDolar::getMonitors(Currencies::dollar, Pages::criptodolar));
    }

    public function testSinMonitoresEuroNoCriptodolar () {
        return $this->assertEmpty(PyDolar::getMonitors(Currencies::euro, Pages::dolartoday));
    }

    // isMonitorValid($currency, $page, $monitor)

    public function testMonitorValido () {
        return $this->assertTrue(PyDolar::isMonitorValid(Currencies::euro, Pages::criptodolar, 'amazon_gift_card'));
    }

    public function testMonitorInvalido() {
        return $this->assertFalse(PyDolar::isMonitorValid(Currencies::dollar, Pages::dolartoday, 'monitorInvalido'));
    }

    // Funciones privadas

    private function testExceptionMonitorIsInvalid () {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Monitor is invalid');
    }

}
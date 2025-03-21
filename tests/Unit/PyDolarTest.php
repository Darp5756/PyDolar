<?php

namespace Darp5756\PyDolar\Tests\Unit;

use Darp5756\PyDolar\Enums\Currencies;
use Darp5756\PyDolar\Enums\FormatDates;
use Darp5756\PyDolar\Enums\Pages;
use Darp5756\PyDolar\PyDolar;
use Exception;
use PHPUnit\Framework\TestCase;

class PyDolarTest extends TestCase {

    // getData($currency, $page, $monitor, $formatDate, $roundedPrice)

    public function testExceptionMonitorInvalido () {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Monitor is invalid');
        PyDolar::getData(Currencies::dollar, Pages::dolartoday, 'monitorInvalido', FormatDates::default, true);
    }

    public function testExceptionSinMonitor () {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Monitor is invalid');
        PyDolar::getData(Currencies::euro, Pages::dolartoday, 'dolartoday', FormatDates::default, true);
    }

    public function testGetData () {
        return $this->assertNotEmpty(PyDolar::getData(Currencies::dollar, Pages::alcambio, 'bcv', FormatDates::default, true));
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

}
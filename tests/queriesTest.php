<?php
setlocale(LC_ALL , "fr_FR" );
date_default_timezone_set("Europe/Paris");

use PHPUnit\Framework\TestCase;

//require_once(dirname(__FILE__)."/../queries.php");
include_once("config.php");
include_once("queries.php");

class queriesTest extends TestCase
{

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    // https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
    public function invokeMethod(&$object, $methodName, array $parameters = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @dataProvider providerTestDateConvSQL
     */
    public function testDateConvSQL($typeIn, $typeOut, $dateIn, $dateOut) {
        $query = new queries;
        $this->assertEquals($dateOut, $this->invokeMethod($query, 'dateConvSQL', array($dateIn, $typeIn, $typeOut)));
        //$this->assertEquals($dateOut, queries::dateConvSQL($dateIn, $typeIn, $typeOut));
    }
    public function providerTestDateConvSQL() {
        $field = '%field%';
        $dtOut = 'DATE(FROM_UNIXTIME(%field%))';
        $tsOut = 'UNIX_TIMESTAMP(%field%)';
        return array(
            array('', '', '', ''),
            array('', 'DATE', '', ''),
            array('', 'TIMESTAMP', '', ''),
            array('DATE', '', '', ''),
            array('TIMESTAMP', '', '', ''),

            array('DATE', 'DATE', '', ''),
            array('DATE', 'TIMESTAMP', '', ''),
            array('TIMESTAMP', 'DATE', '', ''),
            array('TIMESTAMP', 'TIMESTAMP', '', ''),

            array('DATE', 'DATE', 'foo', 'foo'),
            array('DATE', 'TIMESTAMP', 'bar', str_replace('%field%', 'bar', $tsOut)),
            array('TIMESTAMP', 'DATE', 'foo', str_replace('%field%', 'foo', $dtOut)),
            array('TIMESTAMP', 'TIMESTAMP', 'bar', 'bar'),

            array('DATE', 'DATE', $field, $field),
            array('DATE', 'TIMESTAMP', $field, $tsOut),
            array('TIMESTAMP', 'DATE', $field, $dtOut),
            array('TIMESTAMP', 'TIMESTAMP', $field, $field),

            array('', '', $field, $field),
            array('DATE', '', $field, $field),
            array('TIMESTAMP', '', $field, $field),

            array('foo', '', $field, $field),
            array('', 'bar', $field, $field),
        );
    }

    /**
     * @dataProvider providerTestDateConv
     */
    public function testDateConv($typeIn, $typeOut, $dateIn, $dateOut) {
        $query = new queries;
        $this->assertEquals($dateOut, $this->invokeMethod($query, 'dateConv', array($dateIn, $typeIn, $typeOut)));
        //$this->assertEquals($dateOut, queries::dateConv($dateIn, $typeIn, $typeOut));
    }
    public function providerTestDateConv() {
        $ts = '1483138800';
        $dt = '2016-12-31 00:00:00';
        return array(
            array('', '', '', ''),
            array('', 'DATE', '', ''),
            array('', 'TIMESTAMP', '', ''),
            array('DATE', '', '', ''),
            array('TIMESTAMP', '', '', ''),

            array('DATE', 'DATE', '', ''),
            array('DATE', 'TIMESTAMP', '', ''),
            array('TIMESTAMP', 'DATE', '', ''),
            array('TIMESTAMP', 'TIMESTAMP', '', ''),

            array('DATE', 'DATE', 'foo', 'foo'),
            array('DATE', 'TIMESTAMP', 'bar', 'bar'),
            array('TIMESTAMP', 'DATE', 'foo', 'foo'),
            array('TIMESTAMP', 'TIMESTAMP', 'bar', 'bar'),

            array('DATE', 'DATE', $dt, $dt),
            array('DATE', 'TIMESTAMP', $dt, $ts),
            array('TIMESTAMP', 'DATE', $ts, $dt),
            array('TIMESTAMP', 'TIMESTAMP', $ts, $ts),

            array('', '', $dt, $dt),
            array('', '', $ts, $ts),
            array('DATE', '', $dt, $dt),
            array('TIMESTAMP', '', $ts, $ts),

            array('foo', '', $dt, $dt),
            array('', 'bar', $ts, $ts),
        );
    }

    public function testQueryOPTARIF() {
        $format = "SELECT %s FROM %s WHERE %s";
        $this->assertStringMatchesFormat($format, queries::queryOPTARIF());
    }

    /**
     * @dataProvider providerTestQueryMaxPeriod
     */
    public function testQueryMaxPeriod($timestampdebut, $timestampfin, $optarif) {
        $format = "SELECT %s FROM %s WHERE %s";
        $this->assertStringMatchesFormat($format, queries::queryMaxPeriod($timestampdebut, $timestampfin, $optarif));
    }
    public function providerTestQueryMaxPeriod() {
        global $teleinfo;

        return array(
            array('', '', array_keys($teleinfo["PERIODES"])[0]),
            array('', '', null),
        );
    }

    public function testQueryMaxDate() {
        $format = "SELECT %s FROM %s";
        $this->assertStringMatchesFormat($format, queries::queryMaxDate());
    }

    public function testQueryInstantly() {
        $format = "SELECT %s FROM %s";
        $this->assertStringMatchesFormat($format, queries::queryInstantly());
    }

    /**
     * @dataProvider providerTestQueryDaily
     */
    public function testQueryDaily($timestampdebut, $timestampfin, $optarif) {
        $format = "SELECT %s FROM %s";
        $this->assertStringMatchesFormat($format, queries::queryDaily($timestampdebut, $timestampfin, $optarif));
    }
    public function providerTestQueryDaily() {
        global $teleinfo;

        return array(
            array('', '', array_keys($teleinfo["PERIODES"])[0]),
            array('', '', null),
        );
    }

    /**
     * @dataProvider providerTestQueryHistory
     */
    public function testQueryHistory($timestampdebut, $timestampfin, $dateformatsql, $optarif) {
        $format = "SELECT %s FROM %s";
        $this->assertStringMatchesFormat($format, queries::queryHistory($timestampdebut, $timestampfin, $dateformatsql, $optarif));
    }
    public function providerTestQueryHistory() {
        global $teleinfo;

        return array(
            array('', '', '%e', array_keys($teleinfo["PERIODES"])[0]),
            //array('', '', '', null),
        );
    }


}

?>

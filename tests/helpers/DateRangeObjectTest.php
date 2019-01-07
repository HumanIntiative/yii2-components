<?php

use PHPUnit\Framework\TestCase;
use pkpudev\components\helpers\DateRangeObject;
use pkpudev\components\models\Dummy;

class DateRangeObjectTest extends TestCase
{
    // tests
    public function testCreateObject()
    {
        $object = new DateRangeObject('2017-01-01 - 2017-01-31');

        $this->assertEquals('2017-01-01', $object->first_date);

        $this->assertEquals('2017-01-31', $object->last_date);
    }

    public function testCreateObjectButNull()
    {
        $object = new DateRangeObject(null);

        $this->assertEquals(null, $object->first_date);

        $this->assertEquals(null, $object->last_date);
    }

    public function testCreateObjectWithDifferentFormat()
    {
        $object = new DateRangeObject('01/01/2017 - 01/31/2017', 'm/d/Y');

        $this->assertEquals('01/01/2017', $object->first_date);

        $this->assertEquals('01/31/2017', $object->last_date);
    }

    public function testParseFromDB()
    {
        $stub = $this->createMock(Dummy::class);
        $stub->advances_used_date = '[2017-03-14,2017-03-19)';

        $object = DateRangeObject::parseRange($stub->advances_used_date);

        $this->assertEquals('2017-03-14', $object->first_date);
        $this->assertEquals('2017-03-19', $object->last_date);

        $stub->advances_used_date = null;
        $object = DateRangeObject::parseRange($stub->advances_used_date);
        $this->assertEquals(null, $object);
    }

    public function testStaticIsNull()
    {
        $retval = DateRangeObject::isNull('(,)');
        $this->assertTrue(true, $retval);

        $retval = DateRangeObject::isNull(null);
        $this->assertTrue(true, $retval);
    }

    public function testStaticConvertToSql()
    {
        $retval = DateRangeObject::toSQL('2017-01-01 - 2017-01-31');
        $this->assertEquals("['2017-01-01', '2017-01-31']", $retval);

        $retval = DateRangeObject::toSQL('2017-01-01');
        $this->assertEquals("['2017-01-01',)", $retval);

        $retval = DateRangeObject::toSQL(null);
        $this->assertEquals('(,)', $retval);
    }

    public function testStaticConvertToViewForWidget()
    {
        // Null
        $stub = $this->createMock(Dummy::class);
        $stub->advances_used_date = null;
        $retval = DateRangeObject::toWidget($stub->advances_used_date);
        $this->assertEquals(null, $retval);

        $stub->advances_used_date = '[2017-03-14,2017-03-19)';
        $retval = DateRangeObject::toWidget($stub->advances_used_date);
        $this->assertEquals('2017-03-14 - 2017-03-19', $retval);

        $stub->advances_used_date = '[2016-12-15,)';
        $retval = DateRangeObject::toWidget($stub->advances_used_date);
        $this->assertEquals('2016-12-15', $retval);
    }

    public function testStaticConvertToString()
    {
        // Null
        $stub = $this->createMock(Dummy::class);
        $stub->advances_used_date = null;
        $retval = DateRangeObject::toString($stub->advances_used_date, 's/d');
        $this->assertEquals(null, $retval);

        $stub->advances_used_date = '[2017-03-14,2017-03-19)';
        $retval = DateRangeObject::toString($stub->advances_used_date, 's/d');
        $this->assertEquals('2017-03-14 s/d 2017-03-19', $retval);
    }

    public function testStaticConvertToWidgetThenToString()
    {
        $stub = $this->createMock(Dummy::class);
        $stub->advances_used_date = '[2017-06-12,2017-07-15)';
        $view = DateRangeObject::toWidget($stub->advances_used_date);
        $retval = DateRangeObject::toString($view);
        $this->assertEquals('2017-06-12 s/d 2017-07-15', $retval);
    }

    public function testCaseUpdateTglUmBug()
    {
        $stub = $this->createMock(Dummy::class);
        $stub->advances_used_date = '[2017-06-03,2017-06-20)';
        $object = DateRangeObject::parseRange($stub->advances_used_date);

        $this->assertEquals('2017-06-03', $object->first_date);
        $this->assertEquals('2017-06-20', $object->last_date);

        $view = DateRangeObject::toWidget($stub->advances_used_date);
        $this->assertEquals('2017-06-03 - 2017-06-20', $view);

        $sql = DateRangeObject::toSQL($view);
        $this->assertEquals("['2017-06-03', '2017-06-20']", $sql);
    }
}
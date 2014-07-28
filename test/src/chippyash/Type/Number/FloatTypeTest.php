<?php

namespace chippyash\Test\Type\Number;

use chippyash\Type\Number\FloatType;

class FloatTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testFloatTypeConvertsValuesToFloat()
    {
        $t = new FloatType(123.67);
        $this->assertInternalType('float', $t->get());
        $this->assertEquals(123.67, $t->get());
        $t = new FloatType('foo');
        $this->assertInternalType('float', $t->get());
        $this->assertEquals(0, $t->get());
    }

    public function testCanNegateTheNumber()
    {
        $t = new FloatType(2.0);
        $this->assertEquals(-2.0, $t->negate()->get());
    }

    public function testToComplexReturnsComplexType()
    {
        $t = new FloatType(2.0);
        $c = $t->toComplex();
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('2', (string) $c);
        $this->assertEquals(2, $c->r());
        $this->assertEquals(0, $c->i());
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $t1 = new FloatType(2.6);
        $t2 = new FloatType(-2.6);
        $this->assertEquals($t1, $t1->abs());
        $this->assertEquals($t1, $t2->abs());
    }
}

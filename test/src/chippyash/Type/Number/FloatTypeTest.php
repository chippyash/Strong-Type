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

    public function testAsComplexReturnsComplexType()
    {
        $t = new FloatType(2.0);
        $c = $t->asComplex();
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('2', (string) $c);
        $this->assertInstanceOf('chippyash\Type\Number\Rational\RationalType', $c->r());
        $this->assertInstanceOf('chippyash\Type\Number\Rational\RationalType', $c->i());
    }

    public function testAsRationalReturnsRationalType()
    {
        $t = new FloatType(2.0);
        $r = $t->AsRational();
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType', $r);
        $this->assertEquals('2', (string) $r);
        $this->assertInstanceOf('chippyash\Type\Number\IntType', $r->numerator());
        $this->assertInstanceOf('chippyash\Type\Number\IntType', $r->denominator());
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $t = new FloatType(2.0);
        $f = $t->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals($t, $f);
    }

    public function testAsIntTypeReturnsIntType()
    {
        $t = new FloatType(2.0);
        $i = $t->asIntType();
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $i);
        $this->assertEquals(2, (string) $i);
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $t1 = new FloatType(2.6);
        $t2 = new FloatType(-2.6);
        $this->assertEquals($t1, $t1->abs());
        $this->assertEquals($t1, $t2->abs());
    }
}

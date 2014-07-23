<?php

namespace chippyash\Test\Type\Number\Complex;

use chippyash\Type\Number\Complex\ComplexType;
use chippyash\Type\Number\FloatType;

class ComplexTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsFirstParameterToBeFloatType()
    {
        $c = new ComplexType(0);
    }

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsSecondParameterToBeFloatType()
    {
        $c = new ComplexType(new FloatType(1), 0);
    }

    public function testConstructWithTwoFloatTypeParametersReturnsComplexType()
    {
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\ComplexType',
                new ComplexType(new FloatType(1), new FloatType(1)));
    }

    /**
     * @expectedException Exception
     */
    public function testSetFromTypesExpectsFirstParameterToBeFloatType()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(1));
        $c->setFromTypes('foo');
    }

    /**
     * @expectedException Exception
     */
    public function testSetFromTypesExpectsSecondParameterToBeFloatType()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(1));
        $c->setFromTypes(new FloatType(1), 'foo');
    }

    public function testSetFromTypesWithTwoFloatTypeParametersWilRestComplexType()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(1));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\ComplexType',
                $c->setFromTypes(new FloatType(2), new FloatType(4)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects two parameters
     */
    public function testSetWithLessThanTwoParameterThrowsException()
    {
        $c = new ComplexType(new FloatType(4), new FloatType(2));
        $c->set(new FloatType('12'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects two parameters
     */
    public function testSetWithMoreThanTwoParameterThrowsException()
    {
        $c = new ComplexType(new FloatType(4), new FloatType(2));
        $c->set(new FloatType('12'), new FloatType('2'), new FloatType('1'));
    }

    public function testSetThanTwoParameterReturnsComplexType()
    {
        $c = new ComplexType(new FloatType(4), new FloatType(2));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\ComplexType',
                $c->set(new FloatType('12'), new FloatType('2')));
    }

    public function testRReturnsFloat()
    {
        $c = new ComplexType(new FloatType(4), new FloatType(2));
        $this->assertInternalType('float', $c->r());
        $this->assertEquals(4.0, $c->r());
    }

    public function testIReturnsFloat()
    {
        $c = new ComplexType(new FloatType(4), new FloatType(2));
        $this->assertInternalType('float', $c->i());
        $this->assertEquals(2.0, $c->i());
    }

    public function testIsZeroReturnsTrueIfComplexIsZero()
    {
        $c = new ComplexType(new FloatType(0), new FloatType(0));
        $this->assertTrue($c->isZero());
    }

    public function testIsZeroReturnsFalseIfComplexIsNotZero()
    {
        $c = new ComplexType(new FloatType(0), new FloatType(1));
        $this->assertFalse($c->isZero());
        $c2 = new ComplexType(new FloatType(1), new FloatType(0));
        $this->assertFalse($c2->isZero());
    }

    public function testMagicToStringReturnsString()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(2));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('1+2i', $test);
    }

    public function testGetReturnsString()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(2));
        $this->assertInternalType('string', $c->get());
        $this->assertEquals('1+2i', $c->get());
    }

    public function testMagicInvokeReturnsString()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(2));
        $this->assertInternalType('string', $c());
        $this->assertEquals('1+2i', $c());
    }

    public function testIsGaussianForBothPartsBeingIntegerValuesReturnsTrue()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(2));
        $this->assertTrue($c->isGaussian());
    }

    public function testIsGaussianForOnePartNotBeingIntegerValuesReturnsFalse()
    {
        $c = new ComplexType(new FloatType(1.000001), new FloatType(2));
        $this->assertFalse($c->isGaussian());
        $c2 = new ComplexType(new FloatType(1), new FloatType(2.000001));
        $this->assertFalse($c2->isGaussian());
    }
}

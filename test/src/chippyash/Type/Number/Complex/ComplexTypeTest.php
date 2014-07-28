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

    public function testConjugateReturnsCorrectComplexType()
    {
        $c = new ComplexType(new FloatType(2), new FloatType(3));
        $conj = $c->conjugate();
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\ComplexType',
                $conj);
        $this->assertEquals(2, $conj->r());
        $this->assertEquals(-3, $conj->i());
    }

    public function testModulusForZeroComplexNumberIsZero()
    {
        $c = new ComplexType(new FloatType(0), new FloatType(0));
        $this->assertEquals(0, $c->modulus()->get());
    }

    public function testTriangleInequalityForModulus()
    {
        $c1 = new ComplexType(new FloatType(1), new FloatType(2));
        $c2 = new ComplexType(new FloatType(3), new FloatType(4));
        $c1addc2 = new ComplexType(new FloatType($c1->r() + $c2->r()), new FloatType($c1->i() + $c2->i()));
        $mod1 = $c1->modulus();
        $mod2 = $c2->modulus();
        $modc1addc2 = $c1addc2->modulus();

        $this->assertTrue($modc1addc2() <= ($mod1() + $mod2()));
    }

    public function testCommutativeMultiplicationAttributeForModulus()
    {
        $c1 = new ComplexType(new FloatType(1), new FloatType(2));
        $c2 = new ComplexType(new FloatType(3), new FloatType(4));
        $c1mulc2 = new ComplexType(
                new FloatType(($c1->r() * $c2->r()) - ($c1->i() * $c2->i())),
                new FloatType(($c1->i() * $c2->r()) + ($c1->r() * $c2->i())));
        $mod1 = $c1->modulus();
        $mod2 = $c2->modulus();
        $modc1mulc2 = $c1mulc2->modulus();

        $this->assertEquals($modc1mulc2(), $mod1() * $mod2());
    }

    public function testCanNegateTheNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(2));
        $this->assertEquals('-1-2i', $c->negate()->get());
    }

    public function testIsRealReturnsTrueForRealNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(0));
        $this->assertTrue($c->isReal());
    }

    public function testIsRealReturnsFalseForNotRealNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(1));
        $this->assertFalse($c->isReal());
    }

    public function testGetReturnsFloatForRealNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(0));
        $this->assertInternalType('float', $c->get());
    }

    public function testMagicToStringReturnsString()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(2));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('1+2i', $test);

        $c = new ComplexType(new FloatType(1), new FloatType(0));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('1', $test);

    }

    public function testGetReturnsStringForComplexNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(2));
        $this->assertInternalType('string', $c->get());
        $this->assertEquals('1+2i', $c->get());
    }

    public function testMagicInvokeReturnsStringForComplexNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(2));
        $this->assertInternalType('string', $c());
        $this->assertEquals('1+2i', $c());
    }

    public function testMagicInvokeReturnsFloatForRealComplexNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(0));
        $this->assertInternalType('float', $c());
        $this->assertEquals('1', $c());
    }

    public function testToFloatReturnsFloatForRealComplexNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(0));
        $this->assertInternalType('float', $c->toFloat());
        $this->assertEquals(1, $c->toFloat());
    }

    /**
     * @expectedException chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testToFloatThrowsExceptionForNonRealComplexNumber()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(1));
        $c->toFloat();
    }

    public function testToComplexReturnsCloneOfSelf()
    {
        $c = new ComplexType(new FloatType(1), new FloatType(1));
        $c2 = $c->toComplex();
        $this->assertEquals($c, $c2);
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $c1 = new ComplexType(new FloatType(1), new FloatType(2));
        $c2 = new ComplexType(new FloatType(-1), new FloatType(2));
        $c3 = new ComplexType(new FloatType(1), new FloatType(-2));
        $c4 = new ComplexType(new FloatType(-1), new FloatType(-2));
        $this->assertEquals($c1->modulus(), $c1->abs());
        $this->assertEquals($c1->modulus(), $c2->abs());
        $this->assertEquals($c1->modulus(), $c3->abs());
        $this->assertEquals($c1->modulus(), $c4->abs());
    }
}

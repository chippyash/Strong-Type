<?php

namespace chippyash\Test\Type\Number\Rational;

use chippyash\Type\Number\Rational\GMPRationalType;
use chippyash\Type\Number\GMPIntType;
use chippyash\Type\BoolType;

class GMPRationalTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsFirstParameterToBeGMPIntType()
    {
        $r = new GMPRationalType(0);
    }

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsSecondParameterToBeGMPIntType()
    {
        $r = new GMPRationalType(new GMPIntType(1), 0);
    }

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsThirdParameterToBeBoolTypeIfGiven()
    {
        $r = new GMPRationalType(new GMPIntType(1), new GMPIntType(1), 0);
    }

    public function testConstructWithThirdParameterSetFalseWillNotReduce()
    {
        $r = new GMPRationalType(new GMPIntType(4), new GMPIntType(2),
                new BoolType(false));
        $this->assertEquals('4/2', $r);
    }

    public function testNumeratorReturnsInteger()
    {
        $r = new GMPRationalType(new GMPIntType(4), new GMPIntType(2));
        $this->assertEquals(2, $r->numerator()->get());
    }

    public function testDenominatorReturnsInteger()
    {
        $r = new GMPRationalType(new GMPIntType(4), new GMPIntType(2));
        $this->assertEquals(1, $r->denominator()->get());
    }

    public function testNegativeDenominatorNormalizesToNegativeNumerator()
    {
        $r = new GMPRationalType(new GMPIntType(4), new GMPIntType(-3));
        $this->assertEquals(-4, $r->numerator()->get());
        $this->assertEquals(3, $r->denominator()->get());
    }

    public function testGetReturnsGmpType()
    {
        $r = new GMPRationalType(new GMPIntType(1), new GMPIntType(2));
        $this->assertInternalType('float', $r->get());
        $this->assertEquals(0.5, $r->get());
    }

    public function testMagicToStringReturnsStringValue()
    {
        $r = new GMPRationalType(new GMPIntType(1), new GMPIntType(2));
        $this->assertEquals('1/2', $r->__toString());
        $this->assertEquals('1/2', (string) $r);
    }

    public function testGetReturnsIntForWholeFraction()
    {
        $r = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $this->assertInternalType('int', $r->get());
        $this->assertEquals(2, $r->get());
    }

    public function testCanNegateTheNumber()
    {
        $r = new GMPRationalType(new GMPIntType(1), new GMPIntType(2));
        $this->assertEquals(-0.5, $r->negate()->get());
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $r1 = new GMPRationalType(new GMPIntType(1), new GMPIntType(2));
        $r2 = new GMPRationalType(new GMPIntType(-1), new GMPIntType(2));
        $r3 = new GMPRationalType(new GMPIntType(1), new GMPIntType(-2));
        $this->assertEquals($r1->get(), $r1->abs()->get());
        $this->assertEquals($r1->get(), $r2->abs()->get());
        $this->assertEquals($r1->get(), $r3->abs()->get());
    }

    public function testMagicInvokeProxiesToGet()
    {
        $r1 = new GMPRationalType(new GMPIntType(3), new GMPIntType(4));
        $this->assertEquals(3 / 4, $r1());
    }

    public function testSetFromTypesReturnsValue()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $this->assertEquals(3/4, $o->setFromTypes(new GMPIntType(3), new GMPIntType(4))->get());
        $this->assertEquals('2/4', (string) $o->setFromTypes(new GMPIntType(2), new GMPIntType(4), new BoolType(false)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects at least two parameters
     */
    public function testSetExpectsAtLeastTwoParameters()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $o->set('foo');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetProxiesToSetFromTypesWithTwoParametersExpectsGMPIntTypeParameters()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $o->set('foo','bar');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetProxiesToSetFromTypesWithThreeParametersExpectsBoolTypeThirdParameter()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $o->set(new GMPIntType(3), new GMPIntType(4), 'foo');
    }

    public function testSetProxiesToSetFromTypesWithTwoCorrectParameters()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $this->assertEquals(3/4, $o->set(new GMPIntType(3), new GMPIntType(4))->get());
    }

    public function testSetProxiesToSetFromTypesWithThreeCorrectParameters()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $this->assertEquals('2/4', $o->set(new GMPIntType(2), new GMPIntType(4), new BoolType(false)));
    }

    public function testAsComplexReturnsComplexType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $c = $o->asComplex();
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('2', (string) $c); //zero imaginary returns real value
    }

    public function testAsRationalReturnsGMPRationalType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $r = $o->AsRational();
        $this->assertEquals($o, $r);
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $f = $o->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(2, (string) $f);
    }

    public function testAsIntTypeReturnsIntType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $i = $o->AsIntType();
        $this->assertInstanceOf('\chippyash\Type\Number\GMPIntType', $i);
        $this->assertEquals(2, (string) $i);
    }
}

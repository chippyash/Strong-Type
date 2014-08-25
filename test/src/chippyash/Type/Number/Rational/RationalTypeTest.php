<?php

namespace chippyash\Test\Type\Number\Rational;

use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\IntType;
use chippyash\Type\BoolType;

class RationalTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsFirstParameterToBeIntType()
    {
        $r = new RationalType(0);
    }

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsSecondParameterToBeIntType()
    {
        $r = new RationalType(new IntType(1), 0);
    }

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsThirdParameterToBeBoolTypeIfGiven()
    {
        $r = new RationalType(new IntType(1), new IntType(1), 0);
    }

    public function testConstructWithThirdParameterSetFalseWillNotReduce()
    {
        $r = new RationalType(new IntType(4), new IntType(2),
                new BoolType(false));
        $this->assertEquals('4/2', $r);
    }

    public function testNumeratorReturnsInteger()
    {
        $r = new RationalType(new IntType(4), new IntType(2));
        $this->assertEquals(2, $r->numerator()->get());
    }

    public function testDenominatorReturnsInteger()
    {
        $r = new RationalType(new IntType(4), new IntType(2));
        $this->assertEquals(1, $r->denominator()->get());
    }

    public function testNegativeDenominatorNormalizesToNegativeNumerator()
    {
        $r = new RationalType(new IntType(4), new IntType(-3));
        $this->assertEquals(-4, $r->numerator()->get());
        $this->assertEquals(3, $r->denominator()->get());
    }

    public function testGetReturnsFloat()
    {
        $r = new RationalType(new IntType(1), new IntType(2));
        $this->assertInternalType('float', $r->get());
        $this->assertEquals(0.5, $r->get());
    }

    public function testMagicToStringReturnsStringValue()
    {
        $r = new RationalType(new IntType(1), new IntType(2));
        $this->assertEquals('1/2', $r->__toString());
        $this->assertEquals('1/2', (string) $r);
    }

    public function testGetReturnsIntForWholeFraction()
    {
        $r = new RationalType(new IntType(2), new IntType(1));
        $this->assertInternalType('int', $r->get());
        $this->assertEquals(2, $r->get());
    }

    public function testCanNegateTheNumber()
    {
        $r = new RationalType(new IntType(1), new IntType(2));
        $this->assertEquals(-0.5, $r->negate()->get());
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $r1 = new RationalType(new IntType(1), new IntType(2));
        $r2 = new RationalType(new IntType(-1), new IntType(2));
        $r3 = new RationalType(new IntType(1), new IntType(-2));
        $this->assertEquals($r1, $r1->abs());
        $this->assertEquals($r1, $r2->abs());
        $this->assertEquals($r1, $r3->abs());
    }

    public function testMagicInvokeProxiesToGet()
    {
        $r1 = new RationalType(new IntType(3), new IntType(4));
        $this->assertEquals(3 / 4, $r1());
    }

    public function testSetFromTypesReturnsValue()
    {
        $o = new RationalType(new IntType(0), new IntType(1));
        $this->assertEquals(3/4, $o->setFromTypes(new IntType(3), new IntType(4))->get());
        $this->assertEquals('2/4', (string) $o->setFromTypes(new IntType(2), new IntType(4), new BoolType(false)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects at least two parameters
     */
    public function testSetExpectsAtLeastTwoParameters()
    {
        $o = new RationalType(new IntType(0), new IntType(1));
        $o->set('foo');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetProxiesToSetFromTypesWithTwoParametersExpectsIntTypeParameters()
    {
        $o = new RationalType(new IntType(0), new IntType(1));
        $o->set('foo','bar');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetProxiesToSetFromTypesWithThreeParametersExpectsBoolTypeThirdParameter()
    {
        $o = new RationalType(new IntType(0), new IntType(1));
        $o->set(new IntType(3), new IntType(4), 'foo');
    }

    public function testSetProxiesToSetFromTypesWithTwoCorrectParameters()
    {
        $o = new RationalType(new IntType(0), new IntType(1));
        $this->assertEquals(3/4, $o->set(new IntType(3), new IntType(4))->get());
    }

    public function testSetProxiesToSetFromTypesWithThreeCorrectParameters()
    {
        $o = new RationalType(new IntType(0), new IntType(1));
        $this->assertEquals('2/4', $o->set(new IntType(2), new IntType(4), new BoolType(false)));
    }

    public function testAsComplexReturnsComplexType()
    {
        $o = new RationalType(new IntType(2), new IntType(1));
        $c = $o->asComplex();
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('2', (string) $c); //zero imaginary returns real value
    }

    public function testAsRationalReturnsRationalType()
    {
        $o = new RationalType(new IntType(2), new IntType(1));
        $r = $o->AsRational();
        $this->assertEquals($o, $r);
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $o = new RationalType(new IntType(2), new IntType(1));
        $f = $o->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(2, (string) $f);
    }

    public function testAsIntTypeReturnsIntType()
    {
        $o = new RationalType(new IntType(2), new IntType(1));
        $i = $o->AsIntType();
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $i);
        $this->assertEquals(2, (string) $i);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testPowThrowsExceptioIfParameterNotIntType()
    {
        $o = new RationalType(new IntType(2), new IntType(1));
        $o->pow('foo');
    }

    public function testPowWillReturnRationalType()
    {
        $o = new RationalType(new IntType(2), new IntType(3));
        $p = $o->pow(new IntType(2));
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType', $p);
        $this->assertEquals('4/9', (string) $p);
    }

    public function testSqrtWillReturnRationalType()
    {
        $o = new RationalType(new IntType(4), new IntType(9));
        $s = $o->sqrt();
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType', $s);
        $this->assertEquals('2/3', (string) $s);
    }
}

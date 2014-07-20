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

    public function testConstructHasOptionalThirdParameter()
    {
        $r = new RationalType(new IntType(4), new IntType(2));
        $this->assertEquals('2/1', $r);
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
        $this->assertEquals(2, $r->numerator());
    }

    public function testDenominatorReturnsInteger()
    {
        $r = new RationalType(new IntType(4), new IntType(2));
        $this->assertEquals(1, $r->denominator());
    }

    public function testNegativeDenominatorNormalizesToNegativeNumerator()
    {
        $r = new RationalType(new IntType(4), new IntType(-3));
        $this->assertEquals(-4, $r->numerator());
        $this->assertEquals(3, $r->denominator());
    }

    public function testGetReturnsFloat()
    {
        $r = new RationalType(new IntType(1), new IntType(2));
        $this->assertInternalType('float', $r->get());
        $this->assertEquals(0.5, $r->get());
    }
}

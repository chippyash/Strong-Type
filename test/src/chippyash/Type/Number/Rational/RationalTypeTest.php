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
}

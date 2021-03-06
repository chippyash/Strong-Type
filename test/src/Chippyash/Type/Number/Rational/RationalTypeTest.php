<?php

namespace Chippyash\Test\Type\Number\Rational;

use Chippyash\Type\Number\Rational\RationalType;
use Chippyash\Type\Number\IntType;
use Chippyash\Type\BoolType;
use Chippyash\Type\RequiredType;

class RationalTypeTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
    }
    
    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsFirstParameterToBeIntType() {
        if (PHP_MAJOR_VERSION < 7) {
            $r = new RationalType(0);
        } else {
            $this->markTestSkipped('Test incompatible with PHP 7');
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsSecondParameterToBeIntType() {
        if (PHP_MAJOR_VERSION < 7) {
            $r = new RationalType(new IntType(1), 0);
        } else {
            $this->markTestSkipped('Test incompatible with PHP 7');
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsThirdParameterToBeBoolTypeIfGiven() {
        if (PHP_MAJOR_VERSION < 7) {
            $r = new RationalType(new IntType(1), new IntType(1), 0);
        } else {
            $this->markTestSkipped('Test incompatible with PHP 7');
        }
    }

    public function testConstructWithThirdParameterSetFalseWillNotReduce() {
        $r = new RationalType(new IntType(4), new IntType(2), new BoolType(false));
        $this->assertEquals('4/2', $r);
    }

    public function testNumeratorReturnsInteger() {
        $r = new RationalType(new IntType(4), new IntType(2));
        $this->assertEquals(2, $r->numerator()->get());
    }

    public function testDenominatorReturnsInteger() {
        $r = new RationalType(new IntType(4), new IntType(2));
        $this->assertEquals(1, $r->denominator()->get());
    }

    public function testNegativeDenominatorNormalizesToNegativeNumerator() {
        $r = new RationalType(new IntType(4), new IntType(-3));
        $this->assertEquals(-4, $r->numerator()->get());
        $this->assertEquals(3, $r->denominator()->get());
    }

    public function testGetReturnsFloat() {
        $r = new RationalType(new IntType(1), new IntType(2));
        $this->assertInternalType('float', $r->get());
        $this->assertEquals(0.5, $r->get());
    }

    public function testCanNegateTheNumber() {
        $r = new RationalType(new IntType(1), new IntType(2));
        $this->assertEquals(-0.5, $r->negate()->get());
    }

    public function testAbsReturnsAbsoluteValue() {
        $r1 = new RationalType(new IntType(1), new IntType(2));
        $r2 = new RationalType(new IntType(-1), new IntType(2));
        $r3 = new RationalType(new IntType(1), new IntType(-2));
        $this->assertEquals($r1, $r1->abs());
        $this->assertEquals($r1, $r2->abs());
        $this->assertEquals($r1, $r3->abs());
    }

    public function testCloneDoesCloneInnerValue() {
        $r1 = new RationalType(new IntType(1), new IntType(2));
        $clone = clone $r1;
        $clone->set(new IntType(3), new IntType(4));
        $this->assertNotEquals($clone(), $r1());
    }

}

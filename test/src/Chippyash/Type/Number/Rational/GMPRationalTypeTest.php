<?php

namespace Chippyash\Test\Type\Number\Rational;

use Chippyash\Type\Number\Rational\GMPRationalType;
use Chippyash\Type\Number\GMPIntType;
use Chippyash\Type\BoolType;
use Chippyash\Type\RequiredType;

/**
 * @requires extension gmp
 * @runTestsInSeparateProcesses
 */
class GMPRationalTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        RequiredType::getInstance()->set(RequiredType::TYPE_GMP);
    }
    
    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsFirstParameterToBeGMPIntType()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $r = new GMPRationalType(0);
        } else {
            $this->markTestSkipped('Test incompatible with PHP 7');
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsSecondParameterToBeGMPIntType()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $r = new GMPRationalType(new GMPIntType(1), 0);
        } else {
            $this->markTestSkipped('Test incompatible with PHP 7');
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsThirdParameterToBeBoolTypeIfGiven()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $r = new GMPRationalType(new GMPIntType(1), new GMPIntType(1), 0);
        } else {
            $this->markTestSkipped('Test incompatible with PHP 7');
        }
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

    public function testSetReturnsValue()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $this->assertEquals(3/4, $o->set(new GMPIntType(3), new GMPIntType(4))->get());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected 2 parameters, got 1
     */
    public function testSetExpectsAtLeastTwoParameters()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $o->set('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid Type (string) at position 0
     */
    public function testSetProxiesToSetFromTypesWithTwoParametersExpectsGMPIntTypeParameters()
    {
        $o = new GMPRationalType(new GMPIntType(0), new GMPIntType(1));
        $o->set('foo','bar');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected 2 parameters, got 3
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

    public function testAsComplexReturnsComplexType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $c = $o->asComplex();
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('2', (string) $c); //zero imaginary returns real value
    }

    public function testAsRationalReturnsRationalType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $r = $o->AsRational();
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $r);
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $f = $o->asFloatType();
        $this->assertInstanceOf('\Chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(2, (string) $f);
    }

    public function testAsIntTypeReturnsIntType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $i = $o->AsIntType();
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType', $i);
        $this->assertEquals(2, (string) $i);
    }
    
    public function testGmpReturnsArrayOfGmpTypes()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $gmp = $o->gmp();
        $this->assertInternalType('array', $gmp);
        $this->assertTrue($this->gmpTypeCheck($gmp[0]));
        $this->assertTrue($this->gmpTypeCheck($gmp[1]));
    }
    
    public function testAsGmpIntTypeReturnsGmpIntType()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $this->assertInstanceOf('Chippyash\Type\Number\GMPIntType', $o->asGMPIntType());
    }
    
    public function testAsGmpRationalReturnsCloneOfSelf()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $clone = $o->asGMPRational();
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\GMPRationalType', $clone);
        $clone->negate();
        $this->assertNotEquals($clone(), $o());
    }
    
    public function testAsGmpComplexReturnsGmpComplex()
    {
        $o = new GMPRationalType(new GMPIntType(2), new GMPIntType(1));
        $this->assertInstanceOf('Chippyash\Type\Number\Complex\GMPComplexType', $o->asGMPComplex());
    }


    /**
     * Check gmp type depending on PHP version
     *
     * @param mixed $value value to check type of
     * @return boolean true if gmp number else false
     */
    protected function gmpTypeCheck($value)
    {
        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            return is_resource($value) && get_resource_type($value) == 'GMP integer';
        }

        return ($value instanceof \GMP);
    }
}

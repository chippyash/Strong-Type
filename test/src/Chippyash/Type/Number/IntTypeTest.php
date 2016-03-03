<?php

namespace Chippyash\Test\Type\Number;

use Chippyash\Type\Number\IntType;
use Chippyash\Type\RequiredType;

class IntTypeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
    }
    
    public function testIntTypeConvertsValuesToInteger()
    {
        $t = new IntType(12);
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(12, $t->get());
        $t = new IntType('foo');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(0, $t->get());
        $t = new IntType('34');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
        $t = new IntType('34.6');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
    }

    public function testIntTypeCanBeUsedInCalculation()
    {
        $t = new IntType(12);
        $this->assertEquals(24, 2 * $t());
    }

    public function testCanNegateTheNumber()
    {
        $t = new IntType(2);
        $this->assertEquals(-2, $t->negate()->get());
    }

    public function testAsComplexReturnsComplexType()
    {
        $t = new IntType(2);
        $c = $t->AsComplex();
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('2', (string) $c);
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $c->r());
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $c->i());
    }

    public function testAsRationalReturnsRationalType()
    {
        $t = new IntType(2);
        $r = $t->AsRational();
        $this->assertInstanceOf('\Chippyash\Type\Number\Rational\RationalType', $r);
        $this->assertEquals('2', (string) $r);
        $this->assertInstanceOf('Chippyash\Type\Number\IntType', $r->numerator());
        $this->assertInstanceOf('Chippyash\Type\Number\IntType', $r->denominator());
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $t = new IntType(2);
        $f = $t->asFloatType();
        $this->assertInstanceOf('\Chippyash\Type\Number\FloatType', $f);
        $this->assertEquals('2', (string) $f);
    }

    public function testAsIntTypeReturnsIntType()
    {
        $t = new IntType(2);
        $i = $t->asIntType();
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType', $i);
        $this->assertEquals($t, $i);
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $t1 = new IntType(2);
        $t2 = new IntType(-2);
        $this->assertEquals($t1, $t1->abs());
        $this->assertEquals($t1, $t2->abs());
    }

    /**
     * @dataProvider factors
     */
    public function testFactorsReturnsAnArrayOfFactorsOfTheNumber($n, array $factors)
    {
        $i = new IntType($n);
        $this->assertEquals($factors, $i->factors());
    }

    /**
     * @dataProvider factors
     */
    public function testPrimeFactorsReturnsAnArrayOfFactorsOfTheNumber($n, array $ignore, array $pFactors)
    {
        $i = new IntType($n);
        //unwrap the factors - phpUnit does not pass keys in!
        $pf = array();
        foreach($pFactors as $factor) {
            $k = key($factor);
            $pf[$k] = $factor[$k];
        }
        $this->assertEquals($pf, $i->primeFactors());
    }

    /**
     * Example factorizations
     *
     * @return array [[n, factors, primefactors],...]
     */
    public function factors()
    {
        return array (
            array(2,array(1, 2),array(array(2=>1))),
            array(3,array(1, 3),array(array(3=>1))),
            array(4,array(1, 2, 4),array(array(2=>2))),
            array(5,array(1,5),array(array(5=>1))),
            array(6,array(1, 2, 3, 6),array(array(2=>1), array(3=>1))),
            array(7,array(1, 7),array(array(7=>1))),
            array(8,array(1, 2, 4, 8),array(array(2=>3))),
            array(9,array(1, 3, 9),array(array(3=>2))),
            array(10,array(1, 2, 5,10),array(array(5=>1),array(2=>1))),
            array(138,array(1, 2, 3, 6, 23, 46, 69, 138), array(array(2=>1),array(3=>1),array(23=>1))),
            array(1643,array(1, 31, 53, 1643),array(array(31=>1),array(53=>1))),
            array(1644,array(1, 2, 3, 4, 6, 12, 137, 274, 411, 548, 822, 1644),array(array(2=>2), array(3=>1), array(137=>1)))
        );
    }
}

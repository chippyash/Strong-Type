<?php

namespace chippyash\Test\Type\Number;

use chippyash\Type\Number\GMPIntType;
use chippyash\Type\TypeFactory;

/**
 * @requires extension gmp
 * @runTestsInSeparateProcesses
 */
class GMPIntTypeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        TypeFactory::setNumberType(TypeFactory::TYPE_GMP);
    }
    
    public function testGMPIntTypeConvertsValuesToInteger()
    {
        $t = new GMPIntType(12);
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(12, $t->get());
        $t = new GMPIntType('foo');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(0, $t->get());
        $t = new GMPIntType('34');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
        $t = new GMPIntType('34.6');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
    }

    public function testCanNegateTheNumber()
    {
        $t = new GMPIntType(2);
        $this->assertEquals(-2, $t->negate()->get());
    }

    public function testAsComplexReturnsComplexType()
    {
        $t = new GMPIntType(2);
        $c = $t->AsComplex();
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\GMPComplexType', $c);
        $this->assertEquals('2', (string) $c);
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $c->r());
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $c->i());
    }

    public function testAsRationalReturnsRationalType()
    {
        $t = new GMPIntType(2);
        $r = $t->AsRational();
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\GMPRationalType', $r);
        $this->assertEquals('2', (string) $r);
        $this->assertInstanceOf('chippyash\Type\Number\GMPIntType', $r->numerator());
        $this->assertInstanceOf('chippyash\Type\Number\GMPIntType', $r->denominator());
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $t = new GMPIntType(2);
        $f = $t->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals('2', (string) $f);
    }

    public function testAsIntTypeReturnsGMPIntType()
    {
        $t = new GMPIntType(2);
        $this->assertInstanceOf('\chippyash\Type\Number\GMPIntType', $t);
        $this->assertEquals('2', (string) $t->asIntType());
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $t1 = new GMPIntType(2);
        $t2 = new GMPIntType(-2);
        $this->assertEquals($t1(), $t1->abs()->get());
        $this->assertEquals($t1(), $t2->abs()->get());
    }

    /**
     * @dataProvider factors
     */
    public function testFactorsReturnsAnArrayOfFactorsOfTheNumber($n, array $factors)
    {
        $i = new GMPIntType($n);
        $this->assertEquals($factors, $i->factors());
    }

    /**
     * @dataProvider factors
     */
    public function testPrimeFactorsReturnsAnArrayOfFactorsOfTheNumber($n, array $ignore, array $pFactors)
    {
        $i = new GMPIntType($n);
        //unwrap the factors - phpUnit does not pass keys in!
        $pf = [];
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
        return [
            [2,[1, 2],[[2=>1]]],
            [3,[1, 3],[[3=>1]]],
            [4,[1, 2, 4],[[2=>2]]],
            [5,[1,5],[[5=>1]]],
            [6,[1, 2, 3, 6],[[2=>1], [3=>1]]],
            [7,[1, 7],[[7=>1]]],
            [8,[1, 2, 4, 8],[[2=>3]]],
            [9,[1, 3, 9],[[3=>2]]],
            [10,[1, 2, 5,10],[[5=>1],[2=>1]]],
            [138,[1, 2, 3, 6, 23, 46, 69, 138], [[2=>1],[3=>1],[23=>1]]],
            [1643,[1, 31, 53, 1643],[[31=>1],[53=>1]]],
            [1644,[1, 2, 3, 4, 6, 12, 137, 274, 411, 548, 822, 1644],[[2=>2], [3=>1], [137=>1]]]
        ];
    }
    
    public function testAsGmpIntTypeClonesOriginal()
    {
        $i = new GMPIntType(2);
        $this->assertEquals($i, $i->asGMPIntType());
    }
    
    public function testAsGmpComplexReturnsGmpComplexType()
    {
        $i = new GMPIntType(2);
        $this->assertInstanceOf('chippyash\Type\Number\Complex\GMPComplexType', $i->asGMPComplex());
    }
    
    public function testAsGmpRationalReturnsGmpRationalType()
    {
        $i = new GMPIntType(2);
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $i->asGMPRational());
    }
    
    
 }

<?php

namespace chippyash\Test\Type\Number;

use chippyash\Type\Number\IntType;
use chippyash\Type\Number\Rational\RationalTypeFactory;

class IntTypeTest extends \PHPUnit_Framework_TestCase
{

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

    public function testCanNegateTheNumber()
    {
        $t = new IntType(2);
        $this->assertEquals(-2, $t->negate()->get());
    }

    public function testAsComplexReturnsComplexType()
    {
        $t = new IntType(2);
        $c = $t->AsComplex();
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('2', (string) $c);
        $this->assertInstanceOf('chippyash\Type\Number\Rational\RationalType', $c->r());
        $this->assertInstanceOf('chippyash\Type\Number\Rational\RationalType', $c->i());
    }

    public function testAsRationalReturnsRationalType()
    {
        $t = new IntType(2);
        $r = $t->AsRational();
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType', $r);
        $this->assertEquals('2', (string) $r);
        $this->assertInstanceOf('chippyash\Type\Number\IntType', $r->numerator());
        $this->assertInstanceOf('chippyash\Type\Number\IntType', $r->denominator());
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $t = new IntType(2);
        $f = $t->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals('2', (string) $f);
    }

    public function testAsIntTypeReturnsIntType()
    {
        $t = new IntType(2);
        $i = $t->asIntType();
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $i);
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

    /**
     * @dataProvider exponentsPow
     * @param int $exp
     */
    public function testPowReturnsCorrectResult($exp, $result)
    {
        $i = new IntType(2);
        $this->assertEquals($result, $i->pow(new IntType($exp))->get());
    }

    public function exponentsPow()
    {
        return [
            [2, 4],
            [3, 8],
            [4, 16]
        ];
    }

    /**
     * @dataProvider squareRoots
     * @param int $num
     * @param string $result
     */
    public function testSqrtReturnsCorrectResult($num, $result)
    {
        $expected = RationalTypeFactory::fromString($result);
        $i = new IntType($num);
        $this->assertEquals($expected, $i->sqrt());
    }

    public function squareRoots()
    {
        return [
            [1, '1/1'],
            [2, '131836323/93222358'],
            [3,'10240062466522008/5912102821565065'],
            [4,'2/1'],
            [59782,'1894802244/7749589']
        ];
    }
}

<?php

namespace chippyash\Test\Type\Number\Complex;

use chippyash\Type\Number\Complex\ComplexType;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\IntType;

class ComplexTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsFirstParameterToBeFloatType()
    {
        $c = new ComplexType($this->createRationalType(0));
    }

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsSecondParameterToBeFloatType()
    {
        $c = new ComplexType($this->createRationalType(0), 0);
    }

    public function testConstructWithTwoRationalTypeParametersReturnsComplexType()
    {
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\ComplexType',
                new ComplexType($this->createRationalType(0), $this->createRationalType(0)));
    }

    /**
     * @expectedException Exception
     */
    public function testSetFromTypesExpectsFirstParameterToBeRationalType()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $c->setFromTypes('foo');
    }

    /**
     * @expectedException Exception
     */
    public function testSetFromTypesExpectsSecondParameterToBeRationalType()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $c->setFromTypes($this->createRationalType(0), 'foo');
    }

    public function testSetFromTypesWithTwoRationalTypeParametersWillReturnComplexType()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\ComplexType',
                $c->setFromTypes($this->createRationalType(0), $this->createRationalType(0)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects two parameters
     */
    public function testSetWithLessThanTwoParameterThrowsException()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $c->set($this->createRationalType(0));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects two parameters
     */
    public function testSetWithMoreThanTwoParameterThrowsException()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $c->set($this->createRationalType(0),$this->createRationalType(0),$this->createRationalType(0));
    }

    public function testSetThanTwoParameterReturnsComplexType()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\ComplexType',
                $c->set($this->createRationalType(0),$this->createRationalType(0)));
    }

    public function testRReturnsRational()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Rational\RationalType',
                $c->r());
        $this->assertEquals(0, $c->r()->get());
    }

    public function testIReturnsRational()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Rational\RationalType',
                $c->i());
        $this->assertEquals(0, $c->i()->get());
    }

    public function testIsZeroReturnsTrueIfComplexIsZero()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertTrue($c->isZero());
    }

    public function testIsZeroReturnsFalseIfComplexIsNotZero()
    {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(1));
        $this->assertFalse($c->isZero());
        $c2 = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertFalse($c2->isZero());
    }

    public function testIsGaussianForBothPartsBeingIntegerValuesReturnsTrue()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $this->assertTrue($c->isGaussian());
    }

    public function testIsGaussianForOnePartNotBeingIntegerValuesReturnsFalse()
    {
        $nonInt = RationalTypeFactory::fromFloat(2.00001);
        $int = $this->createRationalType(2);
        $c = new ComplexType($nonInt, $int);
        $this->assertFalse($c->isGaussian());
        $c2 = new ComplexType($int, $nonInt);
        $this->assertFalse($c2->isGaussian());
    }

    public function testConjugateReturnsCorrectComplexType()
    {
        $c = new ComplexType($this->createRationalType(2), $this->createRationalType(3));
        $conj = $c->conjugate();
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\ComplexType',
                $conj);
        $this->assertEquals($this->createRationalType(2), $conj->r());
        $this->assertEquals($this->createRationalType(-3), $conj->i());
    }

    public function testModulusForZeroComplexNumberIsZero()
    {
        $r = $this->createRationalType(0);
        $c = new ComplexType($r, $r);
        $this->assertEquals($r, $c->modulus());
    }

    /**
     * for r == a real number
     * z = r+0i
     * |z| = sqrt(r^2 + 0^2)
     *     = sqrt(r^2)
     *     = abs(r)
     */
    public function testModulusForRealReturnsAbsReal()
    {
        $zi = $this->createRationalType(0);
        //test a selection
        $r = -13;
        while ($r<14) {
            $zr = RationalTypeFactory::fromFloat($r);
            $z = new ComplexType($zr, $zi);
            $this->assertEquals(abs($r), $z->modulus()->get());
            $r+=0.3;
        }
    }

    /**
     * mod(c1 + c2) <= mod(c1) + mod(c2)
     *
     * c->modulus = sqrt(cr^2 + ci^2)
     *
     * http://planetmath.org/triangleinequalityofcomplexnumbers
     *
     * example below uses comple number equivalents of real numebrs
     * a = 3+oi
     * b = 4+0i
     * c = 5+0i
     *
     * |a+b| <= |a|+|b| : sqrt(7) <= 3+4 : Sqrt(7) < 7 : true
     * |b+c| <= |b|+|c| : sqrt(9) <= 4+5 : 3 < 9 : true
     * |a+c| <= |a|+|c| : sqrt(8) <= 3+5 : sqrt(8) < 8 : true
     */
    public function testTriangleInequalityForModulus()
    {
        $zero = $this->createRationalType(0);
        $a = new ComplexType($this->createRationalType(3), $zero);
        $b = new ComplexType($this->createRationalType(4), $zero);
        $c = new ComplexType($this->createRationalType(5), $zero);
        $aAddb = new ComplexType($this->createRationalType(7), $zero);
        $aAddc = new ComplexType($this->createRationalType(8), $zero);
        $bAddc = new ComplexType($this->createRationalType(9), $zero);
        $aMod = $a->modulus()->get();
        $bMod = $b->modulus()->get();
        $cMod = $c->modulus()->get();
        $aAddbMod = $aAddb->modulus()->get();
        $aAddcMod = $aAddc->modulus()->get();
        $bAddcMod = $bAddc->modulus()->get();

        //|a+b| <= |a|+|b|
        $this->assertLessThanOrEqual($aAddbMod, ($aMod + $bMod));
        //|b+c| <= |b|+|c|
        $this->assertLessThanOrEqual($bAddcMod, ($bMod + $cMod));
        //|a+c| <= |a|+|c|
        $this->assertLessThanOrEqual($aAddcMod, ($aMod + $cMod));


    }

    /**
     * |c1 * c2| = |c1| * |c2|
     */
    public function testCommutativeMultiplicationAttributeForModulus()
    {
        $c1 = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $c2 = new ComplexType($this->createRationalType(3), $this->createRationalType(4));

        $c1R = $c1->r()->get();
        $c1I = $c1->i()->get();
        $c2R = $c2->r()->get();
        $c2I = $c2->i()->get();
        $nR = ($c1R * $c2R) - ($c1I * $c2I);
        $nI = ($c1I * $c2R) + ($c1R * $c2I);
        $c1mulc2 = new ComplexType(
                RationalTypeFactory::fromFloat($nR),
                RationalTypeFactory::fromFloat($nI)
                );

        $mod1 = $c1->modulus();
        $mod2 = $c2->modulus();
        $modc1mulc2 = $c1mulc2->modulus();

        $this->assertEquals($modc1mulc2(), $mod1() * $mod2());
    }

    public function testCanNegateTheNumber()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $this->assertEquals('-1-2i', $c->negate()->get());
    }

    public function testIsRealReturnsTrueForRealNumber()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertTrue($c->isReal());
    }

    public function testIsRealReturnsFalseForNotRealNumber()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(1));
        $this->assertFalse($c->isReal());
    }

    public function testGetReturnsIntegerForIntegerRealNumber()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertInternalType('int', $c->get());
    }

    public function testGetReturnsFloatForFloatRealNumber()
    {
        $c = new ComplexType(RationalTypeFactory::fromFloat(2.5), $this->createRationalType(0));
        $this->assertInternalType('float', $c->get());
    }

    public function testMagicToStringReturnsString()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('1+2i', $test);

        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('1', $test);

        $c = new ComplexType(RationalTypeFactory::fromFloat(2.5), $this->createRationalType(0));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('5/2', $test);

        $c = new ComplexType(RationalTypeFactory::fromFloat(2.5), $this->createRationalType(-2));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('5/2-2i', $test);

    }

    public function testGetReturnsStringForComplexNumber()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $this->assertInternalType('string', $c->get());
        $this->assertEquals('1+2i', $c->get());
    }

    public function testMagicInvokeReturnsStringForComplexNumber()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $this->assertInternalType('string', $c());
        $this->assertEquals('1+2i', $c());
    }

    public function testMagicInvokeReturnsIntForRealIntegerComplexNumber()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertInternalType('int', $c());
        $this->assertEquals(1, $c());
    }

    public function testMagicInvokeReturnsFloatForRealFloatComplexNumber()
    {
        $c = new ComplexType(RationalTypeFactory::fromFloat(2.5), $this->createRationalType(0));
        $this->assertInternalType('float', $c());
        $this->assertEquals(2.5, $c());
    }

    /**
     * @expectedException chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testToFloatThrowsExceptionForNonRealComplexNumber()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(1));
        $c->toFloat();
    }

    public function testToComplexReturnsCloneOfSelf()
    {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(1));
        $c2 = $c->toComplex();
        $this->assertEquals($c, $c2);
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $c1 = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $c2 = new ComplexType($this->createRationalType(-1), $this->createRationalType(2));
        $c3 = new ComplexType($this->createRationalType(1), $this->createRationalType(-2));
        $c4 = new ComplexType($this->createRationalType(-1), $this->createRationalType(-2));
        $this->assertEquals($c1->modulus(), $c1->abs());
        $this->assertEquals($c1->modulus(), $c2->abs());
        $this->assertEquals($c1->modulus(), $c3->abs());
        $this->assertEquals($c1->modulus(), $c4->abs());
    }

    /**
     * Create a rational type
     *
     * @param int $n
     * @param int $d
     * @return \chippyash\Type\Number\FloatType
     */
    protected function createRationalType($n, $d = 1)
    {
        return new RationalType(new IntType($n), new IntType($d));
    }
}

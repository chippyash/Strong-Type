<?php

namespace chippyash\Test\Type\Number\Complex;

use chippyash\Type\Number\Complex\GMPComplexType;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\Rational\GMPRationalType;
use chippyash\Type\Number\Rational\GMPRationalTypeFactory;
use chippyash\Type\Number\GMPIntType;

class GMPComplexTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsFirstParameterToBeFloatType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0));
    }

    /**
     * @expectedException Exception
     */
    public function testConstructExpectsSecondParameterToBeFloatType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), 0);
    }

    public function testConstructWithTwoGMPRationalTypeParametersReturnsGMPComplexType()
    {
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\GMPComplexType',
                new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0)));
    }

    /**
     * @expectedException Exception
     */
    public function testSetFromTypesExpectsFirstParameterToBeGMPRationalType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $c->setFromTypes('foo');
    }

    /**
     * @expectedException Exception
     */
    public function testSetFromTypesExpectsSecondParameterToBeGMPRationalType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $c->setFromTypes($this->createGMPRationalType(0), 'foo');
    }

    public function testSetFromTypesWithTwoGMPRationalTypeParametersWillReturnGMPComplexType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\GMPComplexType',
                $c->setFromTypes($this->createGMPRationalType(0), $this->createGMPRationalType(0)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects two parameters
     */
    public function testSetWithLessThanTwoParameterThrowsException()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $c->set($this->createGMPRationalType(0));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects two parameters
     */
    public function testSetWithMoreThanTwoParameterThrowsException()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $c->set($this->createGMPRationalType(0),$this->createGMPRationalType(0),$this->createGMPRationalType(0));
    }

    public function testSetThanTwoParameterReturnsGMPComplexType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\GMPComplexType',
                $c->set($this->createGMPRationalType(0),$this->createGMPRationalType(0)));
    }

    public function testRReturnsRational()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Rational\GMPRationalType',
                $c->r());
        $this->assertEquals(0, $c->r()->get());
    }

    public function testIReturnsRational()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Rational\GMPRationalType',
                $c->i());
        $this->assertEquals(0, $c->i()->get());
    }

    public function testIsZeroReturnsTrueIfComplexIsZero()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $this->assertTrue($c->isZero());
    }

    public function testIsZeroReturnsFalseIfComplexIsNotZero()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(1));
        $this->assertFalse($c->isZero());
        $c2 = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(0));
        $this->assertFalse($c2->isZero());
    }

    public function testIsGaussianForBothPartsBeingIntegerValuesReturnsTrue()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $this->assertTrue($c->isGaussian());
    }

//    public function testIsGaussianForOnePartNotBeingIntegerValuesReturnsFalse()
//    {
//        $nonInt = GMPRationalTypeFactory::fromFloat(2.00001);
//        $int = $this->createGMPRationalType(2);
//        $c = new GMPComplexType($nonInt, $int);
//        $this->assertFalse($c->isGaussian());
//        $c2 = new GMPComplexType($int, $nonInt);
//        $this->assertFalse($c2->isGaussian());
//    }

    public function testConjugateReturnsCorrectGMPComplexType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(3));
        $conj = $c->conjugate();
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\GMPComplexType',
                $conj);
        $this->assertEquals($this->createGMPRationalType(2)->get(), $conj->r()->get());
        $this->assertEquals($this->createGMPRationalType(-3)->get(), $conj->i()->get());
    }

    public function testModulusForZeroComplexNumberIsZero()
    {
        $r = $this->createGMPRationalType(0);
        $c = new GMPComplexType($r, $r);
        $this->assertEquals((string) $r, (string) $c->modulus());
    }

    /**
     * for r == a real number
     * z = r+0i
     * |z| = sqrt(r^2 + 0^2)
     *     = sqrt(r^2)
     *     = abs(r)
     */
//    public function testModulusForRealReturnsAbsReal()
//    {
//        $zi = $this->createGMPRationalType(0);
//        //test a selection
//        $r = -13;
//        while ($r<14) {
//            $zr = GMPRationalTypeFactory::fromFloat($r);
//            $z = new GMPComplexType($zr, $zi);
//            $this->assertEquals(abs($r), $z->modulus()->get());
//            $r+=0.3;
//        }
//    }

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
        $zero = $this->createGMPRationalType(0);
        $a = new GMPComplexType($this->createGMPRationalType(3), $zero);
        $b = new GMPComplexType($this->createGMPRationalType(4), $zero);
        $c = new GMPComplexType($this->createGMPRationalType(5), $zero);
        $aAddb = new GMPComplexType($this->createGMPRationalType(7), $zero);
        $aAddc = new GMPComplexType($this->createGMPRationalType(8), $zero);
        $bAddc = new GMPComplexType($this->createGMPRationalType(9), $zero);
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
//    public function testCommutativeMultiplicationAttributeForModulus()
//    {
//        $c1 = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
//        $c2 = new GMPComplexType($this->createGMPRationalType(3), $this->createGMPRationalType(4));
//
//        $c1R = $c1->r()->get();
//        $c1I = $c1->i()->get();
//        $c2R = $c2->r()->get();
//        $c2I = $c2->i()->get();
//        $nR = ($c1R * $c2R) - ($c1I * $c2I);
//        $nI = ($c1I * $c2R) + ($c1R * $c2I);
//        $c1mulc2 = new GMPComplexType(
//                GMPRationalTypeFactory::fromFloat($nR),
//                GMPRationalTypeFactory::fromFloat($nI)
//                );
//
//        $mod1 = $c1->modulus();
//        $mod2 = $c2->modulus();
//        $modc1mulc2 = $c1mulc2->modulus();
//
//        $this->assertEquals($modc1mulc2(), $mod1() * $mod2());
//    }

    public function testModulusReturnsCorrectResult()
    {
        $c1 = new GMPComplexType(
                new GMPRationalType(new GMPIntType(2), new GMPIntType(1)),
                new GMPRationalType(new GMPIntType(12), new GMPIntType(1))
                );
        $c2 = new GMPComplexType(
                new GMPRationalType(new GMPIntType(12), new GMPIntType(1)),
                new GMPRationalType(new GMPIntType(12), new GMPIntType(1))
                );
        //convert to integer to get over any inconsistencies between machines
        //real value 12.165525060596
        $this->assertEquals(12, $c1->modulus()->asIntType()->get());
        //real value 16.970562748477
        $this->assertEquals(16, $c2->modulus()->asIntType()->get());
    }

    public function testCanNegateTheNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $this->assertEquals('-1-2i', $c->negate()->get());
    }

    public function testIsRealReturnsTrueForRealNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(0));
        $this->assertTrue($c->isReal());
    }

    public function testIsRealReturnsFalseForNotRealNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(1));
        $this->assertFalse($c->isReal());
    }

    public function testGetReturnsIntegerForIntegerRealNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(0));
        $this->assertInternalType('int', $c->get());
    }

//    public function testGetReturnsFloatForFloatRealNumber()
//    {
//        $c = new GMPComplexType(GMPRationalTypeFactory::fromFloat(2.5), $this->createGMPRationalType(0));
//        $this->assertInternalType('float', $c->get());
//    }

    public function testMagicToStringReturnsString()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('1+2i', $test);

        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(0));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('1', $test);

        $c = new GMPComplexType(GMPRationalTypeFactory::fromFloat(2.5), $this->createGMPRationalType(0));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('5/2', $test);

        $c = new GMPComplexType(GMPRationalTypeFactory::fromFloat(2.5), $this->createGMPRationalType(-2));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('5/2-2i', $test);

    }

    public function testGetReturnsStringForComplexNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $this->assertInternalType('string', $c->get());
        $this->assertEquals('1+2i', $c->get());
    }

    public function testMagicInvokeReturnsStringForComplexNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $this->assertInternalType('string', $c());
        $this->assertEquals('1+2i', $c());
    }

    public function testMagicInvokeReturnsIntForRealIntegerComplexNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(0));
        $this->assertInternalType('int', $c());
        $this->assertEquals(1, $c());
    }

//    public function testMagicInvokeReturnsFloatForRealFloatComplexNumber()
//    {
//        $c = new GMPComplexType(GMPRationalTypeFactory::fromFloat(2.5), $this->createGMPRationalType(0));
//        $this->assertInternalType('float', $c());
//        $this->assertEquals(2.5, $c());
//    }

    /**
     * @expectedException chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testToFloatThrowsExceptionForNonRealComplexNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(1));
        $c->toFloat();
    }

    public function testToFloatReturnsFloatForRealFloatComplexNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1,2), $this->createGMPRationalType(0));
        $this->assertInternalType('float', $c->toFloat());
    }

    public function testToFloatReturnsIntegerForIntegerFloatComplexNumber()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(0));
        $this->assertInternalType('int', $c->toFloat());
    }

    public function testAsComplexReturnsCloneOfSelf()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(1));
        $c2 = $c->asComplex();
        $this->assertEquals($c, $c2);
    }

    public function testAsRationalReturnsGMPRationalType()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(0));
        $r = $t->AsRational();
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\GMPRationalType', $r);
        $this->assertInstanceOf('\chippyash\Type\Number\GMPIntType', $r->numerator());
        $this->assertInstanceOf('\chippyash\Type\Number\GMPIntType', $r->denominator());
        $this->assertEquals(2, (string) $r);
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(0));
        $f = $t->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(2, (string) $f);
    }

    public function testAsIntTypeReturnsGMPIntType()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(0));
        $i = $t->asIntType();
        $this->assertInstanceOf('\chippyash\Type\Number\GMPIntType', $i);
        $this->assertEquals(2, (string) $i);
    }

    /**
     * @expectedException chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testAsRationalForNonRealThrowsException()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(1));
        $r = $t->AsRational();
    }

    /**
     * @expectedException chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testAsFloatTypeForNonRealThrowsException()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(1));
        $r = $t->asFloatType();
    }

    /**
     * @expectedException chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testAsIntTypeForNonRealThrowsException()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(1));
        $r = $t->asIntType();
    }

    public function testAbsReturnsAbsoluteValue()
    {
        $c1 = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $c2 = new GMPComplexType($this->createGMPRationalType(-1), $this->createGMPRationalType(2));
        $c3 = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(-2));
        $c4 = new GMPComplexType($this->createGMPRationalType(-1), $this->createGMPRationalType(-2));
        $this->assertEquals((string) $c1->modulus(), (string) $c1->abs());
        $this->assertEquals((string) $c1->modulus(), (string) $c2->abs());
        $this->assertEquals((string) $c1->modulus(), (string) $c3->abs());
        $this->assertEquals((string) $c1->modulus(), (string) $c4->abs());
    }

    /**
     * Create a GMP rational type
     *
     * @param int $n
     * @param int $d
     * @return \chippyash\Type\Number\FloatType
     */
    protected function createGMPRationalType($n, $d = 1)
    {
        return new GMPRationalType(new GMPIntType($n), new GMPIntType($d));
    }
}

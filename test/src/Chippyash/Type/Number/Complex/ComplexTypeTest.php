<?php

namespace Chippyash\Test\Type\Number\Complex;

use Chippyash\Type\Number\Complex\ComplexType;
use Chippyash\Type\Number\Complex\ComplexTypeFactory;
use Chippyash\Type\Number\Rational\RationalType;
use Chippyash\Type\Number\Rational\RationalTypeFactory;
use Chippyash\Type\Number\IntType;
use Chippyash\Type\RequiredType;

class ComplexTypeTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
    }
    
    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsFirstParameterToBeFloatType() {
        $c = new ComplexType($this->createRationalType(0));
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsSecondParameterToBeFloatType() {
        $c = new ComplexType($this->createRationalType(0), 0);
    }

    public function testConstructWithTwoRationalTypeParametersReturnsComplexType() {
        $this->assertInstanceOf(
                'Chippyash\Type\Number\Complex\ComplexType', new ComplexType($this->createRationalType(0), $this->createRationalType(0)));
    }

    /**
     * @expectedException Exception
     */
    public function testSetExpectsFirstParameterToBeRationalType() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $c->set('foo');
    }

    /**
     * @expectedException Exception
     */
    public function testSetExpectsSecondParameterToBeRationalType() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $c->set($this->createRationalType(0), 'foo');
    }

    public function testSetWithTwoRationalTypeParametersWillReturnComplexType() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInstanceOf(
                'Chippyash\Type\Number\Complex\ComplexType', $c->set($this->createRationalType(0), $this->createRationalType(0)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected 2 parameters, got 1
     */
    public function testSetWithLessThanTwoParameterThrowsException() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $c->set($this->createRationalType(0));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected 2 parameters, got 3
     */
    public function testSetWithMoreThanTwoParameterThrowsException() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $c->set($this->createRationalType(0), $this->createRationalType(0), $this->createRationalType(0));
    }

    public function testSetWithTwoParameterReturnsComplexType() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInstanceOf(
                'Chippyash\Type\Number\Complex\ComplexType', $c->set($this->createRationalType(0), $this->createRationalType(0)));
    }

    public function testRReturnsRational() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInstanceOf(
                'Chippyash\Type\Number\Rational\RationalType', $c->r());
        $this->assertEquals(0, $c->r()->get());
    }

    public function testIReturnsRational() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInstanceOf(
                'Chippyash\Type\Number\Rational\RationalType', $c->i());
        $this->assertEquals(0, $c->i()->get());
    }

    public function testIsZeroReturnsTrueIfComplexIsZero() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertTrue($c->isZero());
    }

    public function testIsZeroReturnsFalseIfComplexIsNotZero() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(1));
        $this->assertFalse($c->isZero());
        $c2 = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertFalse($c2->isZero());
    }

    public function testIsGaussianForBothPartsBeingIntegerValuesReturnsTrue() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $this->assertTrue($c->isGaussian());
    }

    public function testIsGaussianForOnePartNotBeingIntegerValuesReturnsFalse() {
        $nonInt = RationalTypeFactory::fromFloat(2.00001);
        $int = $this->createRationalType(2);
        $c = new ComplexType($nonInt, $int);
        $this->assertFalse($c->isGaussian());
        $c2 = new ComplexType($int, $nonInt);
        $this->assertFalse($c2->isGaussian());
    }

    public function testConjugateReturnsCorrectComplexType() {
        $c = new ComplexType($this->createRationalType(2), $this->createRationalType(3));
        $conj = $c->conjugate();
        $this->assertInstanceOf(
                'Chippyash\Type\Number\Complex\ComplexType', $conj);
        $this->assertEquals($this->createRationalType(2), $conj->r());
        $this->assertEquals($this->createRationalType(-3), $conj->i());
    }

    public function testModulusForZeroComplexNumberIsZero() {
        $r = $this->createRationalType(0);
        $c = new ComplexType($r, $r);
        $this->assertEquals($r, $c->modulus());
    }

    /**
     * for r == a real number
     * z = r+0i
     * |z| = sqrt(r^2 + 0^2)
     * = sqrt(r^2)
     * = abs(r)
     */
    public function testModulusForRealReturnsAbsReal() {
        $zi = $this->createRationalType(0);
//test a selection
        $r = -13;
        while ($r < 14) {
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
    public function testTriangleInequalityForModulus() {
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
    public function testCommutativeMultiplicationAttributeForModulus() {
        $c1 = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $c2 = new ComplexType($this->createRationalType(3), $this->createRationalType(4));
        $c1R = $c1->r()->get();
        $c1I = $c1->i()->get();
        $c2R = $c2->r()->get();
        $c2I = $c2->i()->get();
        $nR = ($c1R * $c2R) - ($c1I * $c2I);
        $nI = ($c1I * $c2R) + ($c1R * $c2I);
        $c1mulc2 = new ComplexType(
                RationalTypeFactory::fromFloat($nR), RationalTypeFactory::fromFloat($nI)
        );
        $mod1 = $c1->modulus();
        $mod2 = $c2->modulus();
        $modc1mulc2 = $c1mulc2->modulus();
        $this->assertEquals($modc1mulc2(), $mod1() * $mod2());
    }

    public function testModulusReturnsCorrectResult() {
        $c1 = new ComplexType(
                new RationalType(new IntType(2), new IntType(1)), new RationalType(new IntType(12), new IntType(1))
        );
        $c2 = new ComplexType(
                new RationalType(new IntType(12), new IntType(1)), new RationalType(new IntType(12), new IntType(1))
        );
//convert to integer to get over any inconsistencies between machines
//real value 12.165525060596
        $this->assertEquals(12, $c1->modulus()->asIntType()->get());
//real value 16.970562748477
        $this->assertEquals(16, $c2->modulus()->asIntType()->get());
    }

    public function testCanNegateTheNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $this->assertEquals('-1-2i', $c->negate()->get());
    }

    public function testIsRealReturnsTrueForRealNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertTrue($c->isReal());
    }

    public function testIsRealReturnsFalseForNotRealNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(1));
        $this->assertFalse($c->isReal());
    }

    public function testGetReturnsZeroIntegerForZeroComplexNumber() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertInternalType('int', $c->get());
    }

    public function testGetReturnsIntegerForIntegerRealComplexNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertInternalType('int', $c->get());
    }

    public function testGetReturnsFloatForFloatRealComplexNumber() {
        $c = new ComplexType(RationalTypeFactory::fromFloat(2.5), $this->createRationalType(0));
        $this->assertInternalType('float', $c->get());
    }

    public function testGetReturnsStringForNonRealComplexNumber() {
        $c = new ComplexType(RationalTypeFactory::fromFloat(2.5), $this->createRationalType(3.6));
        $this->assertInternalType('string', $c->get());
    }

    public function testMagicToStringReturnsString() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('0', $test);
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

    public function testGetReturnsStringForComplexNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $this->assertInternalType('string', $c->get());
        $this->assertEquals('1+2i', $c->get());
    }

    public function testMagicInvokeReturnsStringForComplexNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(2));
        $this->assertInternalType('string', $c());
        $this->assertEquals('1+2i', $c());
    }

    public function testMagicInvokeReturnsIntForRealIntegerComplexNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertInternalType('int', $c());
        $this->assertEquals(1, $c());
    }

    public function testMagicInvokeReturnsFloatForRealFloatComplexNumber() {
        $c = new ComplexType(RationalTypeFactory::fromFloat(2.5), $this->createRationalType(0));
        $this->assertInternalType('float', $c());
        $this->assertEquals(2.5, $c());
    }

    /**
     * @expectedException Chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testToFloatThrowsExceptionForNonRealComplexNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(1));
        $c->toFloat();
    }

    public function testToFloatReturnsFloatForRealFloatComplexNumber() {
        $c = new ComplexType($this->createRationalType(1, 2), $this->createRationalType(0));
        $this->assertInternalType('float', $c->toFloat());
    }

    public function testToFloatReturnsIntegerForIntegerFloatComplexNumber() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertInternalType('int', $c->toFloat());
    }

    public function testAsComplexReturnsCloneOfSelf() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(1));
        $c2 = $c->asComplex();
        $this->assertEquals($c, $c2);
    }

    public function testAsRationalReturnsRationalType() {
        $t = new ComplexType($this->createRationalType(2), $this->createRationalType(0));
        $r = $t->AsRational();
        $this->assertInstanceOf('\Chippyash\Type\Number\Rational\RationalType', $r);
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType', $r->numerator());
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType', $r->denominator());
        $this->assertEquals(2, (string) $r);
    }

    public function testAsFloatTypeReturnsFloatType() {
        $t = new ComplexType($this->createRationalType(2), $this->createRationalType(0));
        $f = $t->asFloatType();
        $this->assertInstanceOf('\Chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(2, (string) $f);
    }

    public function testAsIntTypeReturnsIntType() {
        $t = new ComplexType($this->createRationalType(2), $this->createRationalType(0));
        $i = $t->asIntType();
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType', $i);
        $this->assertEquals(2, (string) $i);
    }

    /**
     * @expectedException Chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testAsRationalForNonRealThrowsException() {
        $t = new ComplexType($this->createRationalType(2), $this->createRationalType(1));
        $r = $t->AsRational();
    }

    /**
     * @expectedException Chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testAsFloatTypeForNonRealThrowsException() {
        $t = new ComplexType($this->createRationalType(2), $this->createRationalType(1));
        $r = $t->asFloatType();
    }

    /**
     * @expectedException Chippyash\Type\Exceptions\NotRealComplexException
     * @expectedExceptionMessage Not a Real complex type
     */
    public function testAsIntTypeForNonRealThrowsException() {
        $t = new ComplexType($this->createRationalType(2), $this->createRationalType(1));
        $r = $t->asIntType();
    }

    public function testAbsReturnsAbsoluteValue() {
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
     * @dataProvider polars
     */
    public function testRadiusReturnsCorrectValue(ComplexType $c, $r) {
        $this->assertEquals($r, (string) $c->radius());
    }

    /**
     * @dataProvider polars
     */
    public function testThetaReturnsCorrectValue(ComplexType $c, $r, $t) {
        $this->assertEquals($t, (string) $c->theta());
    }

    /**
     * @dataProvider polars
     */
    public function testAsPolarReturnsCorrectValues(ComplexType $c, $r, $t) {
        $polar = $c->asPolar();
        $this->assertEquals($r, (string) $polar['radius']);
        $this->assertEquals($t, (string) $polar['theta']);
    }

    /**
     * @dataProvider polars
     */
    public function testPolarQuadrantReturnsCorrectQuadrant(ComplexType $c, $r, $t, $q) {
        $this->assertEquals($q, $c->polarQuadrant());
    }

    public function testPolarStringForZeroComplexReturnsZeroString() {
        $c = new ComplexType($this->createRationalType(0), $this->createRationalType(0));
        $this->assertEquals('0', $c->polarString());
    }

    public function testPolarStringForNonZeroComplexReturnsNonZeroString() {
        $c = new ComplexType($this->createRationalType(1), $this->createRationalType(0));
        $this->assertEquals('cos 0 + i⋅sin 0', $c->polarString());
        $c = new ComplexType($this->createRationalType(5), $this->createRationalType(0));
        $this->assertEquals('5(cos 0 + i⋅sin 0)', $c->polarString());
        $c = new ComplexType($this->createRationalType(5), $this->createRationalType(2));
        $this->assertEquals('5.385165(cos 0.380506 + i⋅sin 0.380506)', $c->polarString());
    }

    public function polars() {
        return array(
//quadrant 1
            array(new ComplexType($this->createRationalType(5), $this->createRationalType(2)), '73997555/13741001', '15238812/40048769', 1),
//quadrant 2
            array(new ComplexType($this->createRationalType(-5), $this->createRationalType(2)), '73997555/13741001', '266613702/96561163', 2),
//quadrant 3
            array(new ComplexType($this->createRationalType(-5), $this->createRationalType(-2)), '73997555/13741001', '-266613702/96561163', 3),
//quadrant 4
            array(new ComplexType($this->createRationalType(5), $this->createRationalType(-2)), '73997555/13741001', '-15238812/40048769', 4),
        );
    }

    public function testCloneDoesCloneInnerValue() {
        $c1 = new ComplexType($this->createRationalType(2), $this->createRationalType(1));
        $clone = clone $c1;
        $clone->set($this->createRationalType(5), $this->createRationalType(6));
        $this->assertNotEquals($clone(), $c1());
    }

    public function testYouCanGetTheSignOfAComplexNumber()
    {
        $c1 = ComplexTypeFactory::create(1,1);  //unreal positive
        $c2 = ComplexTypeFactory::create(-1,-3);  //unreal negative
        $c3 = ComplexTypeFactory::create(1,0);  //real positive
        $c4 = ComplexTypeFactory::create(-1,0);  //real negative
        $c5 = ComplexTypeFactory::create(0,0);  //zero

        $this->assertEquals(1, $c1->sign());
        $this->assertEquals(1, $c2->sign()); //unreal complex use modulus for sign - therefore always positive
        $this->assertEquals(1, $c3->sign());
        $this->assertEquals(-1, $c4->sign());
        $this->assertEquals(0, $c5->sign());
    }

    /**
     * Create a rational type
     *
     * @param int $n
     * @param int $d
     * @return \Chippyash\Type\Number\FloatType
     */
    protected function createRationalType($n, $d = 1) {
        return new RationalType(new IntType($n), new IntType($d));
    }

}

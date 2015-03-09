<?php

namespace chippyash\Test\Type\Number\Complex;

use chippyash\Type\Number\Complex\GMPComplexType;
use chippyash\Type\Number\Rational\GMPRationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\GMPIntType;
use chippyash\Type\TypeFactory;

/**
 * @requires extension gmp
 * @runTestsInSeparateProcesses
 */
class GMPComplexTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        TypeFactory::setNumberType(TypeFactory::TYPE_GMP);
    }
    
    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsFirstParameterToBeFloatType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0));
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testConstructExpectsSecondParameterToBeRationalType()
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
    public function testSetExpectsFirstParameterToBeGMPRationalType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $c->set('foo');
    }

    /**
     * @expectedException Exception
     */
    public function testSetExpectsSecondParameterToBeGMPRationalType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $c->set($this->createGMPRationalType(0), 'foo');
    }

    public function testSetWithTwoGMPRationalTypeParametersWillReturnGMPComplexType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $this->assertInstanceOf(
                'chippyash\Type\Number\Complex\GMPComplexType',
                $c->set($this->createGMPRationalType(0), $this->createGMPRationalType(0)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected 2 parameters, got 1
     */
    public function testSetWithLessThanTwoParameterThrowsException()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $c->set($this->createGMPRationalType(0));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected 2 parameters, got 3
     */
    public function testSetWithMoreThanTwoParameterThrowsException()
    {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $c->set($this->createGMPRationalType(0),$this->createGMPRationalType(0),$this->createGMPRationalType(0));
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
    public function testCommutativeMultiplicationAttributeForModulus()
    {
        $c1 = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $c2 = new GMPComplexType($this->createGMPRationalType(3), $this->createGMPRationalType(4));

        $c1R = $c1->r()->get();
        $c1I = $c1->i()->get();
        $c2R = $c2->r()->get();
        $c2I = $c2->i()->get();
        $nR = ($c1R * $c2R) - ($c1I * $c2I);
        $nI = ($c1I * $c2R) + ($c1R * $c2I);
        $c1mulc2 = new GMPComplexType(
                RationalTypeFactory::fromFloat($nR),
                RationalTypeFactory::fromFloat($nI)
                );

        $mod1 = $c1->modulus();
        $mod2 = $c2->modulus();
        $modc1mulc2 = $c1mulc2->modulus();

        $this->assertEquals($modc1mulc2(), $mod1() * $mod2());
    }

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

    public function testGetReturnsFloatForFloatRealNumber()
    {
        $c = new GMPComplexType(RationalTypeFactory::fromFloat(2.5), $this->createGMPRationalType(0));
        $this->assertInternalType('float', $c->get());
    }

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

        $c = new GMPComplexType(RationalTypeFactory::fromFloat(2.5), $this->createGMPRationalType(0));
        $test = (string) $c;
        $this->assertInternalType('string', $test);
        $this->assertEquals('5/2', $test);

        $c = new GMPComplexType(RationalTypeFactory::fromFloat(2.5), $this->createGMPRationalType(-2));
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

    public function testMagicInvokeReturnsFloatForRealFloatComplexNumber()
    {
        $c = new GMPComplexType(RationalTypeFactory::fromFloat(2.5), $this->createGMPRationalType(0));
        $this->assertInternalType('float', $c());
        $this->assertEquals(2.5, $c());
    }

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

    public function testAsComplexReturnsNativeComplexType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(1));
        $this->assertInstanceOf('chippyash\Type\Number\Complex\ComplexType', $c->asComplex());
    }

    public function testAsRationalReturnsRationalType()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(0));
        $r = $t->AsRational();
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType', $r);
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $r->numerator());
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $r->denominator());
        $this->assertEquals(2, (string) $r);
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(0));
        $f = $t->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(2, (string) $f);
    }

    public function testAsIntTypeReturnsIntType()
    {
        $t = new GMPComplexType($this->createGMPRationalType(2), $this->createGMPRationalType(0));
        $i = $t->asIntType();
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $i);
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
    
    public function testThetaReturnsGMPRationalType()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $c->theta());
    }
    

    public function testPolarStringForZeroComplexReturnsZeroString() {
        $c = new GMPComplexType($this->createGMPRationalType(0), $this->createGMPRationalType(0));
        $this->assertEquals('0', $c->polarString());
    }

    public function testPolarStringForNonZeroComplexReturnsNonZeroString() {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(0));
        $this->assertEquals('cos 0 + i⋅sin 0', $c->polarString());
        $c = new GMPComplexType($this->createGMPRationalType(5), $this->createGMPRationalType(0));
        $this->assertEquals('5(cos 0 + i⋅sin 0)', $c->polarString());
        $c = new GMPComplexType($this->createGMPRationalType(5), $this->createGMPRationalType(2));
        $this->assertEquals('5.385165(cos 0.380506 + i⋅sin 0.380506)', $c->polarString());
    }
    
    public function testGmpReturnsArray()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $gmp = $c->gmp();
        $this->assertInternalType('array', $gmp);
        $this->assertInternalType('array', $gmp[0]);
        $this->assertInternalType('array', $gmp[1]);
        $this->assertTrue($this->gmpTypeCheck($gmp[0][0]));
        $this->assertTrue($this->gmpTypeCheck($gmp[0][1]));
        $this->assertTrue($this->gmpTypeCheck($gmp[1][0]));
        $this->assertTrue($this->gmpTypeCheck($gmp[1][1]));
    }

    /**
     * @expectedException chippyash\Type\Exceptions\NotRealComplexException
     */
    public function testAsGmpIntTypeThrowsExceptionForNonRealComplex()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $c->asGMPIntType();
    }
    
    public function testAsGmpComplexReturnsClone()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $clone = $c->asGMPComplex();
        $clone->negate();
        $this->assertNotEquals((string) $clone, (string) $c);
    }
    
    public function testAsGmpRationalReturnsGmpRationalForRealComplex()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(0));
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $c->asGMPRational());
    }
    
    /**
     * @expectedException chippyash\Type\Exceptions\NotRealComplexException
     */
    public function testAsGmpRationalThrowsExceptionForNonRealComplex()
    {
        $c = new GMPComplexType($this->createGMPRationalType(1), $this->createGMPRationalType(2));
        $c->asGMPRational();
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

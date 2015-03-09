<?php

namespace chippyash\Test\Type\Number\Complex;

use chippyash\Type\Number\Complex\ComplexTypeFactory;
use chippyash\Type\Number\Rational\GMPRationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\GMPIntType;
use chippyash\Type\TypeFactory;

/**
 * @requires extension gmp
 * @runTestsInSeparateProcesses
 */
class GMPComplexTypeFactoryTest extends \PHPUnit_Framework_TestCase
{

    const CTYPE_NAME = 'chippyash\Type\Number\Complex\GMPComplexType';

    public function setUp() {
        TypeFactory::setNumberType(TypeFactory::TYPE_GMP);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The string representation of the complex number is invalid
     */
    public function testCreateWithInvalidStringAsFirstParameterThrowsException()
    {
        ComplexTypeFactory::create('foo');
    }

    public function testCreateWithValidStringContainingFloatAsFirstParameterReturnsComplexType()
    {
        $c = ComplexTypeFactory::create('-2.0-2.0452i');
        $this->assertInstanceOf(self::CTYPE_NAME, $c);
        $this->assertEquals('-2-5113/2500i', $c());
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $c->r());
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $c->i());
    }

    public function testCreateWithValidStringContainingRationalAsFirstParameterReturnsComplexType()
    {
        $c = ComplexTypeFactory::create('-12/6-2/5i');
        $this->assertInstanceOf(self::CTYPE_NAME, $c);
        $this->assertEquals('-2-2/5i', $c());
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $c->r());
        $this->assertInstanceOf('chippyash\Type\Number\Rational\GMPRationalType', $c->i());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Imaginary part may not be null if real part is not a string
     */
    public function testCreateWithNonStringAsFirstParamAndNullAsSecondParamThrowsException()
    {
        $c = ComplexTypeFactory::create(22);
    }

    /**
     * @expectedException chippyash\Type\Exceptions\InvalidTypeException
     * @expectedExceptionMessage Invalid Type: object for Complex type construction
     */
    public function testCreateWithUnsupportedParamTypesThrowsException()
    {
        $c = ComplexTypeFactory::create(new \DateTime, new \DateTime);
    }

    /**
     * @dataProvider correctParamCombinations
     * 
     */
    public function testCreateWithCorrectParamTypesReturnsComplexType($r, $i)
    {
        $c = ComplexTypeFactory::create($r, $i);
        $this->assertInstanceOf(self::CTYPE_NAME, $c);

    }

    /**
     * For some reason phpunit ignores the requires annotation at the class level
     * - it seems to process data providers first
     */
    public function correctParamCombinations()
    {
        if (!extension_loaded('gmp')) {
            return array(array(2,2));
        }
        return [
            //numeric int
            array(2,2),
            array(2,2.3),
            array(2,new GMPIntType(2)),
            array(2,new FloatType(2.3)),
            //numeric float
            array(1.2,2),
            array(1.2,2.3),
            array(1.2,new GMPIntType(2)),
            array(1.2,new FloatType(2.3)),
            //IntType
            array(new GMPIntType(2),2),
            array(new GMPIntType(2),2.3),
            array(new GMPIntType(2),new GMPIntType(2)),
            array(new GMPIntType(2),new FloatType(2.3)),
            //FloatType
            array(new FloatType(2.3),2),
            array(new FloatType(2.3),2.3),
            array(new FloatType(2.3),new GMPIntType(2)),
            array(new FloatType(2.3),new FloatType(2.3)),
            //rational type
            array(new GMPRationalType(new GMPIntType(1), new GMPIntType(2)), new GMPRationalType(new GMPIntType(3), new GMPIntType(2)))
        ];
    }
    
    /**
     * @dataProvider polars
     */
    public function testCreateFromPolarReturnsComplexType($r, $t)
    {
        $radius = RationalTypeFactory::fromString($r);
        $theta = RationalTypeFactory::fromString($t);
        $p = ComplexTypeFactory::fromPolar($radius, $theta);
        $this->assertInstanceOf(self::CTYPE_NAME, $p);
    }
    
    public function polars()
    {
        return array(
            //quadrant 1
            array('192119201/35675640','15238812/40048769'),
            //quadrant 2
            array('192119201/35675640','266613702/96561163'),
            //quadrant 3
            array('192119201/35675640','-266613702/96561163'),
            //quadrant 4
            array('192119201/35675640','-15238812/40048769'),
        );
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage foo is not a supported number type
     */
    public function testSetBadNumberTypeThrowsEception()
    {
        ComplexTypeFactory::setNumberType('foo');
    }
    
    public function testCreationWillUseGmpAutomaticallyIfItExists()
    {
        ComplexTypeFactory::setNumberType(ComplexTypeFactory::TYPE_DEFAULT);
        $c = ComplexTypeFactory::create('2+3i');
        $this->assertInstanceOf(self::CTYPE_NAME, $c);
    }
}

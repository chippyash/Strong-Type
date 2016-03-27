<?php

namespace Chippyash\Test\Type\Number\Rational;

use Chippyash\Type\Number\Rational\RationalTypeFactory;
use Chippyash\Type\Number\IntType;
use Chippyash\Type\Number\FloatType;
use Chippyash\Type\RequiredType;
use Chippyash\Type\TypeFactory;

/**
 * @requires extension gmp
 * runTestsInSeparateProcesses
 */
class GMPRationalTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    const RAT_TYPE_NAME = 'Chippyash\Type\Number\Rational\GMPRationalType';

    public function setUp()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_GMP);
    }
    
    public function testCreateFromValidStringValueReturnsRatioanalType()
    {
        $this->assertInstanceOf(
                self::RAT_TYPE_NAME,
                RationalTypeFactory::create('34/15'));
        $this->assertInstanceOf(
                self::RAT_TYPE_NAME,
                RationalTypeFactory::create('34/-15'));
    }

    public function testCreateFromNumericValueWithNoDenominatorSpecifiedReturnsRatioanalType()
    {
        $this->assertInstanceOf(
                self::RAT_TYPE_NAME,
                RationalTypeFactory::create(2));
        $this->assertInstanceOf(
                self::RAT_TYPE_NAME,
                RationalTypeFactory::create(-2.6));
    }

    public function testCreateFromNumericValueWithDenominatorSpecifiedReturnsRatioanalType()
    {
        $this->assertInstanceOf(
                self::RAT_TYPE_NAME,
                RationalTypeFactory::create(34, -15));
    }

    public function testCreateFromIntTypesReturnsRationalType()
    {
        $this->assertInstanceOf(
            self::RAT_TYPE_NAME,
            RationalTypeFactory::create(new IntType(34), new IntType(15)));

    }

    public function testCreateFromIntTypeNumeratorAndNoDenominatorReturnsRationalType()
    {
        $this->assertInstanceOf(
            self::RAT_TYPE_NAME,
            RationalTypeFactory::create(new IntType(34)));

    }

    public function testCreateFromIntTypeNumeratorAndNumericDenominatorReturnsRationalType()
    {
        $this->assertInstanceOf(
            self::RAT_TYPE_NAME,
            RationalTypeFactory::create(new IntType(34), 15));

    }

    public function testCreateFromNumericNumeratorAndIntTypeDenominatorReturnsRationalType()
    {
        $this->assertInstanceOf(
            self::RAT_TYPE_NAME,
            RationalTypeFactory::create(34, new IntType(15)));

    }

    public function testCreateFromFloatTypeReturnsRationalType()
    {
        $this->assertInstanceOf(
            self::RAT_TYPE_NAME,
            RationalTypeFactory::create(new FloatType(34.34)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The string representation of the rational is invalid
     */
    public function testCreateFromInvalidStringThrowsException()
    {
        $this->assertInstanceOf(
                self::RAT_TYPE_NAME,
                RationalTypeFactory::create('3415'));
    }

    /**
     * @expectedException Chippyash\Type\Exceptions\InvalidTypeException
     * @expectedExceptionMessage Invalid Type: integer:object for Rational type construction
     */
    public function testCreateFromUnsupportedTypeForDenominatorThrowsException()
    {
        RationalTypeFactory::create(2,new \stdClass());
    }

    public function testFromFloatUsesDefaultToleranceIfNotGiven()
    {
        $this->assertEquals('25510582/80143857', (string) RationalTypeFactory::fromFloat(M_1_PI));
    }

    public function testFromFloatUsesAcceptsPhpFloatToleranceValue()
    {
        $this->assertEquals(
                '78256779/245850922',
                (string) RationalTypeFactory::fromFloat(M_1_PI, 1e-17));
    }

    public function testFromFloatUsesAcceptsFloatTypeToleranceValue()
    {
        $this->assertEquals(
                '78256779/245850922',
                (string) RationalTypeFactory::fromFloat(M_1_PI, new FloatType(1e-17)));
    }

    public function testFromFloatWithZeroValueReturnsZeroAsString()
    {
        $this->assertEquals(
                '0',
                (string) RationalTypeFactory::fromFloat(0.0));
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testSetDefaultFromFloatToleranceIsStatic()
    {
        RationalTypeFactory::setDefaultFromFloatTolerance(1e-5);
        $this->assertEquals('113/355', (string) RationalTypeFactory::fromFloat(M_1_PI));
        RationalTypeFactory::setDefaultFromFloatTolerance(1e-15);
        $this->assertEquals('25510582/80143857', (string) RationalTypeFactory::fromFloat(M_1_PI));
    }
    
    public function testSetNumberTypeToDefaultWillSetGmpIfAvailable()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_DEFAULT);

        $this->assertInstanceOf('Chippyash\Type\Number\Rational\GMPRationalType', TypeFactory::create('rational', 2));
    }
}

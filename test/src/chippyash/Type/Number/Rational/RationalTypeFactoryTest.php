<?php

namespace chippyash\Test\Type\Number\Rational;

use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\IntType;
use chippyash\Type\Number\FloatType;

class RationalTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    const RAT_TYPE_NAME = 'chippyash\Type\Number\Rational\RationalType';

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
     * @expectedException chippyash\Type\Exceptions\InvalidTypeException
     * @expectedExceptionMessage Invalid Type: object:NULL for Rational type construction
     */
    public function testCreateFromUnsupportedTypeForNumeratorThrowsException()
    {
        RationalTypeFactory::create(new \stdClass());
    }

    /**
     * @expectedException chippyash\Type\Exceptions\InvalidTypeException
     * @expectedExceptionMessage Invalid Type: integer:object for Rational type construction
     */
    public function testCreateFromUnsupportedTypeForDenominatorThrowsException()
    {
        RationalTypeFactory::create(2,new \stdClass());
    }

    public function testFromFloatUsesDefaultToleranceIfNotGiven()
    {
        $this->assertEquals('113/355', (string) RationalTypeFactory::fromFloat(M_1_PI));
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
}

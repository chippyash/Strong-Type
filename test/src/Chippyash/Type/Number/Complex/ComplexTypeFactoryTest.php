<?php

namespace Chippyash\Test\Type\Number\Complex;

use Chippyash\Type\Number\Complex\ComplexTypeFactory;
use Chippyash\Type\Number\Rational\RationalType;
use Chippyash\Type\Number\Rational\RationalTypeFactory;
use Chippyash\Type\Number\FloatType;
use Chippyash\Type\Number\IntType;
use Chippyash\Type\RequiredType;

class ComplexTypeFactoryTest extends \PHPUnit_Framework_TestCase
{

    const CTYPE_NAME = 'Chippyash\Type\Number\Complex\ComplexType';

    public function setUp() {
        RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
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
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $c->r());
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $c->i());
    }

    public function testCreateWithValidStringContainingRationalAsFirstParameterReturnsComplexType()
    {
        $c = ComplexTypeFactory::create('-12/6-2/5i');
        $this->assertInstanceOf(self::CTYPE_NAME, $c);
        $this->assertEquals('-2-2/5i', $c());
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $c->r());
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $c->i());
    }

    /**
     * @expectedException Chippyash\Type\Exceptions\InvalidTypeException
     * @expectedExceptionMessage Invalid Type: object for Complex type construction
     */
    public function testCreateWithUnsupportedParamTypesThrowsException()
    {
        $c = ComplexTypeFactory::create(new \DateTime, new \DateTime);
    }

    /**
     * @dataProvider correctParamCombinations
     */
    public function testCreateWithCorrectParamTypesReturnsComplexType($r, $i)
    {
        $c = ComplexTypeFactory::create($r, $i);
        $this->assertInstanceOf(self::CTYPE_NAME, $c);

    }

    public function correctParamCombinations()
    {
        return array(
            //numeric int
            array(2,2),
            array(2,2.3),
            array(2,new IntType(2)),
            array(2,new FloatType(2.3)),
            //numeric float
            array(1.2,2),
            array(1.2,2.3),
            array(1.2,new IntType(2)),
            array(1.2,new FloatType(2.3)),
            //IntType
            array(new IntType(2),2),
            array(new IntType(2),2.3),
            array(new IntType(2),new IntType(2)),
            array(new IntType(2),new FloatType(2.3)),
            //FloatType
            array(new FloatType(2.3),2),
            array(new FloatType(2.3),2.3),
            array(new FloatType(2.3),new IntType(2)),
            array(new FloatType(2.3),new FloatType(2.3)),
            //rational type
            array(new RationalType(new IntType(1), new IntType(2)), new RationalType(new IntType(3), new IntType(2)))
        );
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
}

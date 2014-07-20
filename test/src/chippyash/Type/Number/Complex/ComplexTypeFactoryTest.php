<?php

namespace chippyash\Test\Type\Number\Complex;

use chippyash\Type\Number\Complex\ComplexTypeFactory;
use chippyash\Type\Number\Complex\ComplexType;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\IntType;

class ComplexTypeFactoryTest extends \PHPUnit_Framework_TestCase
{

    const CTYPE_NAME = 'chippyash\Type\Number\Complex\ComplexType';

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The string representation of the complex number is invalid
     */
    public function testCreateWithInvalidStringAsFirstParameterThrowsException()
    {
        ComplexTypeFactory::create('foo');
    }

    public function testCreateWithValidStringAsFirstParameterReturnsComplexType()
    {
        $c = ComplexTypeFactory::create('-2.0-2.0452i');
        $this->assertInstanceOf(self::CTYPE_NAME, $c);
        $this->assertEquals('-2-2.0452i', $c());
        $this->assertEquals(-2.0, $c->r());
        $this->assertEquals(-2.0452, $c->i());
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
     */
    public function testCreateWithCorrectParamTypesReturnsComplexType($r, $i)
    {
        $c = ComplexTypeFactory::create($r, $i);
        $this->assertInstanceOf(self::CTYPE_NAME, $c);

    }

    public function correctParamCombinations()
    {
        return [
            //numeric int
            [2,2],
            [2,2.3],
            [2,new IntType(2)],
            [2,new FloatType(2.3)],
            //numeric float
            [1.2,2],
            [1.2,2.3],
            [1.2,new IntType(2)],
            [1.2,new FloatType(2.3)],
            //IntType
            [new IntType(2),2],
            [new IntType(2),2.3],
            [new IntType(2),new IntType(2)],
            [new IntType(2),new FloatType(2.3)],
            //FloatType
            [new FloatType(2.3),2],
            [new FloatType(2.3),2.3],
            [new FloatType(2.3),new IntType(2)],
            [new FloatType(2.3),new FloatType(2.3)],
        ];
    }
}

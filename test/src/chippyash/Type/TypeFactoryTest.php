<?php

namespace chippyash\Test\Type;

use chippyash\Type\TypeFactory;

class TypeFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers chippyash\Type\TypeFactory::create
     * @covers chippyash\Type\AbstractType::get
     */
    public function testFactoryCreateMethodReturnsCorrectType()
    {
        $var = TypeFactory::create('int', 54);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals(54, $var->get());
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $var);

        $var = TypeFactory::create('string', 'foo');
        $this->assertInternalType('string', $var->get());
        $this->assertEquals('foo', $var->get());
        $this->assertInstanceOf('\chippyash\Type\String\StringType', $var);

        $var = TypeFactory::create('float', 54.98);
        $this->assertInternalType('float', $var->get());
        $this->assertEquals(54.98, $var->get());
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $var);

        $var = TypeFactory::create('bool', true);
        $this->assertInternalType('bool', $var->get());
        $this->assertTrue($var->get());
        $this->assertInstanceOf('\chippyash\Type\BoolType', $var);

        $var = TypeFactory::create('boolean', true);
        $this->assertInternalType('bool', $var->get());
        $this->assertTrue($var->get());
        $this->assertInstanceOf('\chippyash\Type\BoolType', $var);

        $var = TypeFactory::create('Digit', 54.98);
        $this->assertInternalType('string', $var->get());
        $this->assertEquals('5498', $var->get());
        $this->assertInstanceOf('\chippyash\Type\String\DigitType', $var);

        $var = TypeFactory::create('whole', 54);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals('54', $var->get());
        $this->assertInstanceOf('\chippyash\Type\Number\WholeIntType', $var);

        $var = TypeFactory::create('natural', 54);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals('54', $var->get());
        $this->assertInstanceOf('\chippyash\Type\Number\NaturalIntType', $var);

        $var = TypeFactory::create('rational', 54);
        $this->assertInternalType('float', $var->get());
        $this->assertEquals(54.0, $var->get());
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType', $var);

        $var = TypeFactory::create('rational', 54, 2);
        $this->assertInternalType('float', $var->get());
        $this->assertEquals(27.0, $var->get());
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType', $var);

        $var = TypeFactory::create('complex', 54, 2);
        $this->assertInternalType('string', $var->get());
        $this->assertEquals('54+2i', $var->get());
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $var);
    
        $var = TypeFactory::create('complex', 54);
        $this->assertInternalType('float', $var->get());
        $this->assertEquals(54, $var->get());
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $var);

    }

    /**
     * @expectedException chippyash\Type\Exceptions\InvalidTypeException
     */
    public function testCreateInvalidTypeThrowsException()
    {
        $var = TypeFactory::create('foo', 54.98);
    }

    /**
     * @covers chippyash\Type\TypeFactory::createInt
     */
    public function testCreateIntReturnsIntType()
    {
        $this->assertInstanceOf('\chippyash\Type\Number\IntType',
                Typefactory::createInt(67));
    }

    /**
     * @covers chippyash\Type\TypeFactory::createFloat
     */
    public function testCreateFloatReturnsFloatType()
    {
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType',
                Typefactory::createFloat(67));
    }

    /**
     * @covers chippyash\Type\TypeFactory::createString
     */
    public function testCreateStringReturnsStringType()
    {
        $this->assertInstanceOf('\chippyash\Type\String\StringType',
                Typefactory::createString(67));
    }

    /**
     * @covers chippyash\Type\TypeFactory::createBool
     */
    public function testCreateBoolReturnsBoolType()
    {
        $this->assertInstanceOf('\chippyash\Type\BoolType',
                Typefactory::createBool(true));
        $this->assertInstanceOf('\chippyash\Type\BoolType',
                Typefactory::createBool(false));
        $this->assertInstanceOf('\chippyash\Type\BoolType',
                Typefactory::createBool(0));
        $this->assertInstanceOf('\chippyash\Type\BoolType',
                Typefactory::createBool(1));
        $this->assertInstanceOf('\chippyash\Type\BoolType',
                Typefactory::createBool('foo'));
    }

    /**
     * @covers chippyash\Type\TypeFactory::createDigit
     */
    public function testCreateDigitReturnsDigitType()
    {
        $this->assertInstanceOf('\chippyash\Type\String\DigitType',
                Typefactory::createDigit(67));
        $this->assertInstanceOf('\chippyash\Type\String\DigitType',
                Typefactory::createDigit('67'));
        $this->assertInstanceOf('\chippyash\Type\String\DigitType',
                Typefactory::createDigit('ab'));
    }

    /**
     * @covers chippyash\Type\TypeFactory::createWhole
     */
    public function testCreateWholeReturnsWholeIntType()
    {
        $this->assertInstanceOf('\chippyash\Type\Number\WholeIntType',
                Typefactory::createWhole(67));
    }

    /**
     * @covers chippyash\Type\TypeFactory::createNatural
     */
    public function testCreateNaturalReturnsNaturalIntType()
    {
        $this->assertInstanceOf('\chippyash\Type\Number\NaturalIntType',
                Typefactory::createNatural(67));
    }

    /**
     * @covers chippyash\Type\TypeFactory::createRational
     */
    public function testCreateRationalReturnsRationalType()
    {
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType',
                Typefactory::createRational(54));
        $this->assertInstanceOf('\chippyash\Type\Number\Rational\RationalType',
                Typefactory::createRational(54, 2));
    }

    /**
     * @covers chippyash\Type\TypeFactory::createComplex
     */
    public function testCreateComplexReturnsComplexType()
    {
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType',
                Typefactory::createComplex(54, 2));
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType',
                Typefactory::createComplex('54+2i'));
    }

}

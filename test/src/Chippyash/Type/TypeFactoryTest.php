<?php

namespace Chippyash\Test\Type;

use Chippyash\Type\Number\IntType;
use Chippyash\Type\TypeFactory;
use Chippyash\Type\RequiredType;

class TypeFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
    }
    
    public function testFactoryCreateMethodReturnsCorrectType()
    {
        $var = TypeFactory::create('int', 54);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals(54, $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType', $var);

        $var = TypeFactory::create('string', 'foo');
        $this->assertInternalType('string', $var->get());
        $this->assertEquals('foo', $var->get());
        $this->assertInstanceOf('\Chippyash\Type\String\StringType', $var);

        $var = TypeFactory::create('float', 54.98);
        $this->assertInternalType('float', $var->get());
        $this->assertEquals(54.98, $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\FloatType', $var);

        $var = TypeFactory::create('bool', true);
        $this->assertInternalType('bool', $var->get());
        $this->assertTrue($var->get());
        $this->assertInstanceOf('\Chippyash\Type\BoolType', $var);

        $var = TypeFactory::create('boolean', true);
        $this->assertInternalType('bool', $var->get());
        $this->assertTrue($var->get());
        $this->assertInstanceOf('\Chippyash\Type\BoolType', $var);

        $var = TypeFactory::create('Digit', 54.98);
        $this->assertInternalType('string', $var->get());
        $this->assertEquals('5498', $var->get());
        $this->assertInstanceOf('\Chippyash\Type\String\DigitType', $var);

        $var = TypeFactory::create('whole', 54);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals('54', $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\WholeIntType', $var);

        $var = TypeFactory::create('natural', 54);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals('54', $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\NaturalIntType', $var);

        $var = TypeFactory::create('rational', 54);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals(54, $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\Rational\RationalType', $var);

        //will reduce
        $var = TypeFactory::create('rational', 54, 2);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals(27, $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\Rational\RationalType', $var);

        $var = TypeFactory::create('complex', 54, 2);
        $this->assertInternalType('string', $var->get());
        $this->assertEquals('54+2i', $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType', $var);

        $var = TypeFactory::create('complex', 54);
        $this->assertInternalType('int', $var->get());
        $this->assertEquals(54, $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType', $var);

        $var = TypeFactory::create('complex', '1-5i');
        $this->assertInternalType('string', $var->get());
        $this->assertEquals('1-5i', $var->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType', $var);

    }

    /**
     * @expectedException \Chippyash\Type\Exceptions\InvalidTypeException
     */
    public function testCreateInvalidTypeThrowsException()
    {
        TypeFactory::create('foo', 54.98);
    }

    public function testCreateIntReturnsIntType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType',
                Typefactory::createInt(67));
    }

    public function testCreateIntWithANumericTypeReturnsAnIntType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType',
            Typefactory::createInt(new IntType(1)));
    }

    public function testCreateFloatReturnsFloatType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\FloatType',
                Typefactory::createFloat(67));
    }

    public function testCreateFloatWithANumericTypeReturnsAFloatType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\FloatType',
            Typefactory::createFloat(new IntType(1)));
    }

    public function testCreateStringReturnsStringType()
    {
        $this->assertInstanceOf('\Chippyash\Type\String\StringType',
                Typefactory::createString(67));
    }

    public function testCreateBoolReturnsBoolType()
    {
        $this->assertInstanceOf('\Chippyash\Type\BoolType',
                Typefactory::createBool(true));
        $this->assertInstanceOf('\Chippyash\Type\BoolType',
                Typefactory::createBool(false));
        $this->assertInstanceOf('\Chippyash\Type\BoolType',
                Typefactory::createBool(0));
        $this->assertInstanceOf('\Chippyash\Type\BoolType',
                Typefactory::createBool(1));
        $this->assertInstanceOf('\Chippyash\Type\BoolType',
                Typefactory::createBool('foo'));
    }

    public function testCreateDigitReturnsDigitType()
    {
        $this->assertInstanceOf('\Chippyash\Type\String\DigitType',
                Typefactory::createDigit(67));
        $this->assertInstanceOf('\Chippyash\Type\String\DigitType',
                Typefactory::createDigit('67'));
        $this->assertInstanceOf('\Chippyash\Type\String\DigitType',
                Typefactory::createDigit('ab'));
    }

    public function testCreateWholeReturnsWholeIntType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\WholeIntType',
                Typefactory::createWhole(67));
    }

    public function testCreateWholeWithANumericTypeReturnsAWholeIntType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\WholeintType',
            Typefactory::createWhole(new IntType(1)));
    }

    public function testCreateNaturalReturnsNaturalIntType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\NaturalIntType',
                Typefactory::createNatural(67));
    }

    public function testCreateNaturalWithANumericTypeReturnsANaturalIntType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\NaturalintType',
            Typefactory::createNatural(new IntType(1)));
    }

    public function testCreateRationalReturnsRationalType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\Rational\RationalType',
                Typefactory::createRational(54));
        $this->assertInstanceOf('\Chippyash\Type\Number\Rational\RationalType',
                Typefactory::createRational(54, 2));
    }

    public function testCreateComplexReturnsComplexType()
    {
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType',
                Typefactory::createComplex(54, 2));
        $this->assertEquals('54+2i', Typefactory::createComplex(54,2)->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType',
                Typefactory::createComplex(54)); //real number 54+0i
        $this->assertEquals(54, Typefactory::createComplex(54)->get());
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType',
                Typefactory::createComplex('54+2i'));
        $this->assertEquals('54+2i', Typefactory::createComplex('54+2i')->get());
        $complexThree = TypeFactory::createComplex(
            TypeFactory::createRational(TypeFactory::createInt(3), TypeFactory::createInt(1)),
            TypeFactory::createRational(TypeFactory::createInt(-3), TypeFactory::createInt(2))
        );
        $this->assertEquals('3-3/2i', $complexThree->get());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage 'foo' is no valid numeric for IntType
     */
    public function testCreateIntWithNonNumericThrowsException()
    {
        TypeFactory::createInt('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage 'foo' is no valid numeric for WholeIntType
     */
    public function testCreateWholeIntWithNonNumericThrowsException()
    {
        TypeFactory::createWhole('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage 'foo' is no valid numeric for NaturalIntType
     */
    public function testCreateNaturalIntWithNonNumericThrowsException()
    {
        TypeFactory::createNatural('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage 'foo' is no valid numeric for FloatType
     */
    public function testCreateFloatWithNonNumericThrowsException()
    {
        TypeFactory::createFloat('foo');
    }

    /**
     * @dataProvider numericTypes
     */
    public function testCreateWithNumericTypeInterfaceParameterReturnsNumericTypeInterface($required, $nType)
    {
        $this->assertInstanceOf(
                'Chippyash\Type\Interfaces\NumericTypeInterface',
                TypeFactory::create($required, $nType));
    }

    public function numericTypes()
    {
        //need to do this as data is established before setUp() is called
        RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
        $type = TypeFactory::create('complex', '2+0i');
        return array(
//            array('int', $type),
//            array('whole', $type),
//            array('natural', $type),
//            array('float', $type),
//            array('rational', $type),
            array('complex', $type),
        );
    }

    /**
     * @requires extension gmp
     * @runInSeparateProcess
     */
    public function testSetNumberTypeToDefaultWillSetGmpIfAvailable()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_GMP);
        $this->assertInstanceOf('Chippyash\Type\Number\GMPIntType', TypeFactory::create('int', 2));
    }
    
    /**
     * @requires extension gmp
     * @runInSeparateProcess
     */
    public function  testCreatingWholeIntsViaTypeFactoryUnderGmpWillReturnGMPIntType()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_GMP);
        $this->assertInstanceOf('Chippyash\Type\Number\GMPIntType', TypeFactory::create('whole', -1));
    }
    
    /**
     * @requires extension gmp
     * @runInSeparateProcess
     */
    public function  testCreatingNaturalIntsViaTypeFactoryUnderGmpWillReturnGMPIntType()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_GMP);
        $this->assertInstanceOf('Chippyash\Type\Number\GMPIntType', TypeFactory::create('natural', 0));
    }
    
    /**
     * @requires extension gmp
     * @runInSeparateProcess
     */
    public function  testCreatingFloatsViaTypeFactoryUnderGmpWillReturnGMPRationalType()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_GMP);
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\GMPRationalType', TypeFactory::create('float', 2/3));
    }
}

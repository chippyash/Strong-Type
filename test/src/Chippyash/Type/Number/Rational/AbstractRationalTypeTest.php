<?php

namespace Chippyash\Test\Type\Number\Rational;

use Chippyash\Type\Number\IntType;
use Chippyash\Type\RequiredType;


class AbstractRationalTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Mock
     * @var \Chippyash\Type\Number\Rational\AbstractRationalType
     */
    protected $object;

    public function setUp()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
        $this->object = $this->getMockForAbstractClass(
                'Chippyash\Type\Number\Rational\AbstractRationalType',
                array(new IntType(3), new IntType(4)));
    }

    public function testMagicInvokeProxiesToGet()
    {
        $o = $this->object;
        $this->assertEquals(3 / 4, $o());
    }

    public function testSetReturnsObject()
    {
        $o = $this->object;
        $this->assertInstanceOf(
                'Chippyash\Type\Number\Rational\AbstractRationalType',
                $o->set(new IntType(3), new IntType(4)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected 2 parameters, got 1
     */
    public function testSetExpectsAtLeastTwoParameters()
    {
        $this->object->set('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid Type (string) at position 0
     */
    public function testSetProxiesToSetFromTypesWithTwoParametersExpectsIntTypeParameters()
    {
        $this->object->set('foo','bar');
    }

    public function testNumeratorReturnsValue()
    {
        $this->assertEquals(new IntType(3), $this->object->numerator());
    }

    public function testDenominatorReturnsValue()
    {
        $this->assertEquals(new IntType(4), $this->object->denominator());
    }

    public function testGetReturnsValue()
    {
        $o = $this->object;
        $this->assertEquals(0.75, $o->get());
    }

    public function testAsComplexReturnsComplexType()
    {
        $c = $this->object->asComplex();
        $this->assertInstanceOf('\Chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('3/4', (string) $c);
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $c->r());
        $this->assertInstanceOf('Chippyash\Type\Number\Rational\RationalType', $c->i());
    }

    public function testAsRationalReturnsRationalType()
    {
        $o = $this->object;
        $r = $o->AsRational();
        $this->assertEquals($o, $r);
    }

    public function testAsFloatTypeReturnsFloatType()
    {
        $o = $this->object;
        $f = $o->asFloatType();
        $this->assertInstanceOf('\Chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(0.75, $f());
    }

    public function testAsIntTypeReturnsIntType()
    {
        $o = $this->object;
        $i = $o->AsIntType();
        $this->assertInstanceOf('\Chippyash\Type\Number\IntType', $i);
        $this->assertEquals(0, $i());
    }
}

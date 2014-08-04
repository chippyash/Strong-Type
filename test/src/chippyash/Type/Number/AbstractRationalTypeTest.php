<?php

namespace chippyash\Test\Type\Number;

use chippyash\Type\Number\IntType;
use chippyash\Type\BoolType;

class AbstractRationalTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Mock
     * @var chippyash\Type\Number\AbstractRationalType
     */
    protected $object;

    public function setUp()
    {
        $this->object = $this->getMockForAbstractClass(
                'chippyash\Type\Number\Rational\AbstractRationalType',
                [new IntType(3), new IntType(4), new BoolType(true)]);
    }

    public function testMagicInvokeProxiesToGet()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('get')
                ->will($this->returnValue(3 / 4));
        $this->assertEquals(3 / 4, $o());
    }

    public function testSetFromTypesReturnsValue()
    {
        $o = $this->object;
        $o->expects($this->any())
                ->method('setFromTypes')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $o->set(new IntType(3), new IntType(4)));
        $this->assertEquals('foo', $o->set(new IntType(3), new IntType(4)), new BoolType(false));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage set() expects at least two parameters
     */
    public function testSetExpectsAtLeastTwoParameters()
    {
        $this->object->set('foo');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetProxiesToSetFromTypesWithTwoParametersExpectsIntTypeParameters()
    {
        $this->object->set('foo','bar');
    }
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetProxiesToSetFromTypesWithThreeParametersExpectsBoolTypeThirdParameter()
    {
        $this->object->set(new IntType(3), new IntType(4), 'foo');
    }

    public function testSetProxiesToSetFromTypesWithTwoCorrectParameters()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('setFromTypes')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $o->set(new IntType(3), new IntType(4)));
    }

    public function testSetProxiesToSetFromTypesWithThreeCorrectParameters()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('setFromTypes')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $o->set(new IntType(3), new IntType(4), new BoolType(false)));
    }

    public function testNumeratorReturnsValue()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('numerator')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $o->numerator());
    }

    public function testDenominatorReturnsValue()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('denominator')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $o->denominator());
    }

    public function testGetReturnsValue()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('get')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $o->get());
    }

    public function testMagicToStringReturnsValue()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('__toString')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $o->__toString());
    }

    public function testAsComplexReturnsComplexType()
    {
        $o = $this->object;
        $o->expects($this->any())
                ->method('numerator')
                ->will($this->returnValue(new IntType(2)));
        $o->expects($this->any())
                ->method('denominator')
                ->will($this->returnValue(new IntType(1)));
        $c = $o->asComplex();
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('2', (string) $c);
        $this->assertInstanceOf('chippyash\Type\Number\Rational\RationalType', $c->r());
        $this->assertInstanceOf('chippyash\Type\Number\Rational\RationalType', $c->i());
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
        $o->expects($this->any())
                ->method('get')
                ->will($this->returnValue(2));
        $f = $o->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(2, (string) $f);
    }

    public function testAsIntTypeReturnsIntType()
    {
        $o = $this->object;
        $o->expects($this->any())
                ->method('get')
                ->will($this->returnValue(2));
        $i = $o->AsIntType();
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $i);
        $this->assertEquals(2, (string) $i);
    }
}

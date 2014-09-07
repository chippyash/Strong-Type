<?php

namespace chippyash\Test\Type\Number\Rational;

use chippyash\Type\Number\IntType;

class AbstractRationalTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Mock
     * @var chippyash\Type\Number\Rational\AbstractRationalType
     */
    protected $object;

    public function setUp()
    {
        $this->object = $this->getMockForAbstractClass(
                'chippyash\Type\Number\Rational\AbstractRationalType',
                [new IntType(3), new IntType(4)]);
    }

    public function testMagicInvokeProxiesToGet()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('getAsNativeType')
                ->will($this->returnValue(3 / 4));
        $this->assertEquals(3 / 4, $o());
    }

    public function testSetReturnsObject()
    {
        $o = $this->object;
        $this->assertInstanceOf(
                'chippyash\Type\Number\Rational\AbstractRationalType', 
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
        $o->expects($this->once())
                ->method('getAsNativeType')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $o->get());
    }

    public function testAsComplexReturnsComplexType()
    {
        $c = $this->object->asComplex();
        $this->assertInstanceOf('\chippyash\Type\Number\Complex\ComplexType', $c);
        $this->assertEquals('3/4', (string) $c);
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
        $o->expects($this->once())
                ->method('getAsNativeType')
                ->will($this->returnValue(0.75));       
        $f = $o->asFloatType();
        $this->assertInstanceOf('\chippyash\Type\Number\FloatType', $f);
        $this->assertEquals(0.75, $f());
    }

    public function testAsIntTypeReturnsIntType()
    {
        $o = $this->object;
        $o->expects($this->once())
                ->method('getAsNativeType')
                ->will($this->returnValue(0.75));  
        $i = $o->AsIntType();
        $this->assertInstanceOf('\chippyash\Type\Number\IntType', $i);
        $this->assertEquals(0, $i());
    }
}

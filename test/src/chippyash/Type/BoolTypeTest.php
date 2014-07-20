<?php

namespace chippyash\Test\Type;

use chippyash\Type\BoolType;

class BoolTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var chippyash\Type\BoolType
     */
    protected $object;

    public function setUp()
    {
        $this->object = new BoolType(true);
    }

    /**
     * @expectedException Exception
     */
    public function testConstructWithoutValueThrowsException()
    {
        $this->object = new BoolType();
    }

    public function testGetReturnsBoolean()
    {
        $this->assertInternalType('bool', $this->object->get());
        $this->object->set('foo');
        $this->assertInternalType('bool', $this->object->get());
        $this->object->set('');
        $this->assertInternalType('bool', $this->object->get());
        $this->object->set(0);
        $this->assertInternalType('bool', $this->object->get());
        $this->object->set(1);
        $this->assertInternalType('bool', $this->object->get());
        $this->object->set(27.6);
        $this->assertInternalType('bool', $this->object->get());
    }

    public function testGetReturnsOnlyTrueOrFalse()
    {
        $this->assertTrue($this->object->get());
        $this->object->set(false);
        $this->assertFalse($this->object->get());
        $this->object->set('foo');
        $this->assertTrue($this->object->get());
        $this->object->set('');
        $this->assertFalse($this->object->get());
        $this->object->set(0);
        $this->assertFalse($this->object->get());
        $this->object->set(1);
        $this->assertTrue($this->object->get());
        $this->object->set(27.6);
        $this->assertTrue($this->object->get());
    }

    public function testMagicToStringReturnsString()
    {
        $this->assertEquals('true', $this->object);
        $this->object->set(false);
        $this->assertEquals('false', $this->object);
        $this->object->set('foo');
        $this->assertEquals('true', $this->object);
        $this->object->set('');
        $this->assertEquals('false', $this->object);
        $this->object->set(0);
        $this->assertEquals('false', $this->object);
        $this->object->set(1);
        $this->assertEquals('true', $this->object);
        $this->object->set(27.6);
        $this->assertEquals('true', $this->object);
    }

}

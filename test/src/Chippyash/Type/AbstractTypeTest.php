<?php

namespace Chippyash\Test\Type;

class AbstractTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractType
     */
    protected $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass('Chippyash\Type\AbstractType',
                array(null));
        $this->object->expects($this->any())
                ->method('typeOf')
                ->will($this->returnArgument(0));
    }

    /**
     * @covers Chippyash\Type\AbstractType::__construct
     */
    public function testConstructorWillReturnAbstractType()
    {
        $object = $this->getMockForAbstractClass('Chippyash\Type\AbstractType',
                array(null));
        $this->assertInstanceOf('Chippyash\Type\AbstractType', $object);
    }

    /**
     * @covers Chippyash\Type\AbstractType::set
     * @covers Chippyash\Type\AbstractType::get
     */
    public function testSetFollowedByGetReturnsAValue()
    {
        $this->object->set('value');
        $this->assertEquals('value', $this->object->get());
    }

    /**
     * @covers Chippyash\Type\AbstractType::__toString
     * @covers Chippyash\Type\AbstractType::set
     */
    public function testMagicToStringReturnsAString()
    {
        $this->object->set(96);
        $this->assertEquals('96', (string) $this->object);
    }

    /**
     * @covers Chippyash\Type\AbstractType::__invoke
     * @covers Chippyash\Type\AbstractType::set
     * @covers Chippyash\Type\AbstractType::get
     */
    public function testMagicInvokeReturnsValue()
    {
        $this->object->set(96);
        $o = $this->object;
        $this->assertEquals(96, $o());
    }
    
    public function testCloneDoesCloneInnerValue()
    {
        $this->object->set(96);
        $clone = clone $this->object;
        $clone->set(104);
        $this->assertNotEquals($clone(), $this->object->get());
    }
}

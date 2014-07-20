<?php

namespace chippyash\Test\Type;

class AbstractTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractType
     */
    protected $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass('chippyash\Type\AbstractType',
                array(null));
        $this->object->expects($this->any())
                ->method('typeOf')
                ->will($this->returnArgument(0));
    }

    /**
     * @covers chippyash\Type\AbstractType::__construct
     */
    public function testConstructorWillReturnAbstractType()
    {
        $object = $this->getMockForAbstractClass('chippyash\Type\AbstractType',
                array(null));
        $this->assertInstanceOf('chippyash\Type\AbstractType', $object);
    }

    /**
     * @covers chippyash\Type\AbstractType::set
     * @covers chippyash\Type\AbstractType::get
     */
    public function testSetFollowedByGetReturnsAValue()
    {
        $this->object->set('value');
        $this->assertEquals('value', $this->object->get());
    }

    /**
     * @covers chippyash\Type\AbstractType::__toString
     * @covers chippyash\Type\AbstractType::set
     */
    public function testMagicToStringReturnsAString()
    {
        $this->object->set(96);
        $this->assertEquals('96', (string) $this->object);
    }

    /**
     * @covers chippyash\Type\AbstractType::__invoke
     * @covers chippyash\Type\AbstractType::set
     * @covers chippyash\Type\AbstractType::get
     */
    public function testMagicInvokeReturnsValue()
    {
        $this->object->set(96);
        $o = $this->object;
        $this->assertEquals(96, $o());
    }
}

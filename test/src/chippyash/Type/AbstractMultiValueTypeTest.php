<?php

namespace chippyash\Test\Type;

use chippyash\Type\Number\IntType;

class stubMVT extends \chippyash\Type\AbstractMultiValueType
{
    /**
     * map of values for this type
     * @var array
     */
    protected $valueMap = array(
        0 => array('name' => 'foo', 'class' => 'chippyash\Type\Interfaces\NumericTypeInterface'),
        1 => array('name' => 'bar', 'class' => 'integer')
);
    
    public function test()
    {
        $this->typeOf('foo');
    }
    protected function getAsNativeType(){
        return $this->value['foo']->get() + $this->value['bar'];
    }
}

/**
 * Covers ares not covered by higher order tests
 */
class AbstractMultiValueTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractType
     */
    protected $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass('chippyash\Type\AbstractMultiValueType');
    }
    
    public function testMagicToStringReturnsEmptyString()
    {
        $this->assertEquals('', (string) $this->object);
    }
    
    public function testGetInvokesGetAsNativeType()
    {
        $this->object->expects($this->once())
                ->method('getAsNativeType')
                ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->object->get());
    }
    
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage typeOf() method not defined for AbstractMultiValueType
     */
    public function testCallToSetFromTypesThrowsException()
    {
        $o = new stubMVT(new IntType(1), 1);
        $o->test();
    }
    
    public function testClassesAndNativeTypesCanBeUsedToDefineValueMap()
    {
        $o = new stubMVT(new IntType(1), 1);
        $o->set(new IntType(2), 2);
        $this->assertEquals(4, $o());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid Type (stdClass) at position 0
     */
    public function testSetFromTypesCatchesInvaliObjectTypes()
    {
        $o = new stubMVT(new \stdClass(), 1);
    }
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid Type (string) at position 1
     */
    
    public function testSetFromTypesCatchesInvaliNativeTypes()
    {
        $o = new stubMVT(new IntType(1), 'foo');
    }
}

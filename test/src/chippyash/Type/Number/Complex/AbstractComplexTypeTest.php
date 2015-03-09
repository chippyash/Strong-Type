<?php

namespace chippyash\Test\Type\Number\Complex;

use chippyash\Type\Number\IntType;
use chippyash\Type\TypeFactory;

/**
 * covers the few areas not covered by CompleType tests
 */
class AbstractComplexTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Mock
     * @var chippyash\Type\Number\Complex\AbstractComplexType
     */
    protected $object;

    public function setUp()
    {
        TypeFactory::setNumberType(TypeFactory::TYPE_NATIVE);
        $this->object = $this->getMockForAbstractClass(
                'chippyash\Type\Number\Complex\AbstractComplexType',
            array(new IntType(3), new IntType(4)));
    }

    public function testRadiusAndAbsProxyToModulus()
    {
        $this->object->expects($this->any())
                ->method('modulus')
                ->will($this->returnValue(2));
        $this->assertEquals(2, $this->object->modulus());
        $this->assertEquals(2, $this->object->abs());
        $this->assertEquals(2, $this->object->radius());
    }
    
    public function testThetaWillReturnValue()
    {
        $this->object->expects($this->any())
                ->method('theta')
                ->will($this->returnValue(2));
        $this->assertEquals(2, $this->object->theta());
        
    }
    
    public function testPolarStringWillReturnValue()
    {
        $this->object->expects($this->any())
                ->method('polarString')
                ->will($this->returnValue('r(cosθ + i⋅sinθ)'));
        $this->assertEquals('r(cosθ + i⋅sinθ)', $this->object->polarString());
        
    }
    
}

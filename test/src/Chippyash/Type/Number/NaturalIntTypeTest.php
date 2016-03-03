<?php

namespace Chippyash\Test\Type\Number;

use Chippyash\Type\Number\NaturalIntType;
use Chippyash\Type\RequiredType;

class NaturalIntTypeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
    }
    
    public function testNaturalIntTypeConvertsToInteger()
    {
        $t = new NaturalIntType(12);
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(12, $t->get());
        $t = new NaturalIntType('34');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
        $t = new NaturalIntType('34.6');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
    }

    /**
     * @expectedException Chippyash\Type\Exceptions\InvalidTypeException
     * @expectedExceptionMessage Invalid Type: 0 < 1 for natural integer type
     */
    public function testConstructNaturalIntWithIntegerLessThanOneThrowsException()
    {
        $t = new NaturalIntType(0);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Negate not supported for Natural Int Types
     */
    public function testCannotNegateTheNumber()
    {
        $t = new NaturalIntType(12);
        $t->negate();
    }
}

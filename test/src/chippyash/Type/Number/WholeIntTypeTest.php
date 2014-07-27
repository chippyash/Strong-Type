<?php

namespace chippyash\Test\Type\Number;

use chippyash\Type\Number\WholeIntType;

class WholeIntTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testWholeIntTypeConvertsToInteger()
    {
        $t = new WholeIntType(12);
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(12, $t->get());
        $t = new WholeIntType('foo');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(0, $t->get());
        $t = new WholeIntType('34');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
        $t = new WholeIntType('34.6');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
    }

    /**
     * @expectedException chippyash\Type\Exceptions\InvalidTypeException
     * @expectedExceptionMessage Invalid Type: -1 < 0 for whole integer type
     */
    public function testConstructWholeIntWithIntegerLessThanZeroThrowsException()
    {
        $t = new WholeIntType(-1);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Negate not supported for Whole Int Types
     */
    public function testCannotNegateTheNumber()
    {
        $t = new WholeIntType(12);
        $t->negate();
    }    
}

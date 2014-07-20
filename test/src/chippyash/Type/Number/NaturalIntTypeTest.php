<?php

namespace chippyash\Test\Type\Number;

use chippyash\Type\Number\NaturalIntType;

class NaturalIntTypeTest extends \PHPUnit_Framework_TestCase
{

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
     * @expectedException chippyash\Type\Exceptions\InvalidTypeException
     * @expectedExceptionMessage Invalid Type: 0 < 1 for natural integer type
     */
    public function testConstructNaturalIntWithIntegerLessThanOneThrowsException()
    {
        $t = new NaturalIntType(0);
    }

}

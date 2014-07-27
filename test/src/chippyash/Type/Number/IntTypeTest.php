<?php

namespace chippyash\Test\Type\Number;

use chippyash\Type\Number\IntType;

class IntTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testIntTypeConvertsValuesToInteger()
    {
        $t = new IntType(12);
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(12, $t->get());
        $t = new IntType('foo');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(0, $t->get());
        $t = new IntType('34');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
        $t = new IntType('34.6');
        $this->assertInternalType('int', $t->get());
        $this->assertEquals(34, $t->get());
    }
    
    public function testCanNegateTheNumber()
    {
        $t = new IntType(2);
        $this->assertEquals(-2, $t->negate()->get());
    }

}

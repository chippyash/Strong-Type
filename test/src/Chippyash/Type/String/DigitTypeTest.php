<?php

namespace Chippyash\Test\Type\String;

use Chippyash\Type\String\DigitType;

class DigitTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testDigitTypeConvertsStringsWithNumbersStrippingNonDigits()
    {
        $t = new DigitType('12345');
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('12345', $t->get());
        $t = new DigitType('12345abc');
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('12345', $t->get());
        $t = new DigitType(96);
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('96', $t->get());
        $t = new DigitType('abc');
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('', $t->get());
        $t = new DigitType('123.45');
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('12345', $t->get());

    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Object of class stdClass could not be converted to string
     */
    public function testConvertNonStringableTypeThrowsException()
    {
        $t = new DigitType(new \stdClass());
    }
}

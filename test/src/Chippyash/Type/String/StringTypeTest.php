<?php

namespace Chippyash\Test\Type\String;

use Chippyash\Type\String\StringType;

class StringTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testStringTypeConvertsBaseTypesToString()
    {
        $t = new StringType('foo');
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('foo', $t->get());
        $t = new StringType(96);
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('96', $t->get());
        $t = new StringType(34.96);
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('34.96', $t->get());

        $t = new StringType(true);
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('1', $t->get());
        $t = new StringType(false);
        $this->assertInternalType('string', $t->get());
        $this->assertEquals('', $t->get());
    }

    public function testStringTypeProxiesMagicInvokeToGet()
    {
        $t = new StringType('foo');
        $this->assertEquals($t(), $t->get());
    }

    public function testStringTypeCanBeUsedInStringConcatenation()
    {
        $t = new StringType('foo');
        $this->assertEquals('the word is foo', "the word is {$t}");
    }
}

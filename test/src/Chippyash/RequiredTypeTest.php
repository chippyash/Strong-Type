<?php
/**
 * Strong-Type
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2016, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Test\Type;

use Chippyash\Type\RequiredType;

class RequiredTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequiredType
     */
    protected $sut;

    protected function setUp()
    {
        $this->sut = RequiredType::getInstance();
    }

    public function testYouCannotConstructClassDirectly()
    {
        $reflection = new \ReflectionClass('\Chippyash\Type\RequiredType');
        $constructor = $reflection->getConstructor();
        $this->assertFalse($constructor->isPublic());
    }

    public function testYouCanGetASingletonInstance()
    {
        $this->assertInstanceOf('Chippyash\Type\RequiredType', $this->sut);
    }

    /**
     * @runInSeparateProcess
     */
    public function testYouCanGetTheCurrentRequiredType()
    {
        if (extension_loaded('gmp')) {
            $type = RequiredType::TYPE_GMP;
        } else {
            $type = RequiredType::TYPE_NATIVE;
        }
        $this->assertEquals($type, $this->sut->get());
    }

    public function testYouCanSetTheRequiredType()
    {
        $this->assertEquals(RequiredType::TYPE_NATIVE, $this->sut->set(RequiredType::TYPE_NATIVE)->get());
        $this->assertEquals(RequiredType::TYPE_GMP, $this->sut->set(RequiredType::TYPE_GMP)->get());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSettingAnInvalidTypeWillThrowAnException()
    {
        $this->sut->set('foo');
    }
}

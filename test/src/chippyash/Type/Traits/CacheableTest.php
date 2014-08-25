<?php
namespace chippyash\Test\Type\Traits;

use chippyash\Type\Traits\Cacheable;
use Zend\Cache\Storage\Adapter\Memory;

class stubCacheableTrait
{
    use Cacheable;

    public function testSetCacheItem($key, $value)
    {
        return $this->setCacheItem($key, $value);
    }

    public function testGetCacheItem($key)
    {
        return $this->getCacheItem($key);
    }
}

class CacheableTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected $cache;

    public function setUp()
    {
        $this->cache = new Memory();
        $this->object = new stubCacheableTrait();
    }

    public function testSetCacheSetsTheActualCache()
    {
        $this->object->setCache($this->cache);
        $this->checkCacheByReflection($this->cache, $this->object);
    }

    public function testSetCacheItemSetsTheItemIfNotAlreadyExisting()
    {
        $this->object->setCache($this->cache);
        $this->object->testSetCacheItem('foo', 'bar');
        $this->assertEquals('bar', $this->cache->getItem('foo'));
    }

    public function testSetCacheItemSetsTheItemIfAlreadyExisting()
    {
        $this->object->setCache($this->cache);
        $this->object->testSetCacheItem('foo', 'bar');
        $this->assertEquals('bar', $this->cache->getItem('foo'));
        $this->object->testSetCacheItem('foo', 'baz');
        $this->assertEquals('baz', $this->cache->getItem('foo'));
    }

    public function testGetItemReturnsItemPreviouslySet()
    {
        $this->object->setCache($this->cache);
        $this->object->testSetCacheItem('foo', 'bar');
        $this->assertEquals('bar', $this->object->testGetCacheItem('foo'));
    }

    public function testGetItemReturnsNullForUnknownItem()
    {
        $this->object->setCache($this->cache);
        $this->assertNull($this->object->testGetCacheItem('boo'));
    }
    
    protected function checkCacheByReflection($cache, $class)
    {
        $refl = new \ReflectionClass($class);
        $iProperties = $refl->getProperties(\ReflectionProperty::IS_PROTECTED);
        $found = false;
        foreach ($iProperties as $property) {
            if ($property->getName() == 'cache') {
                $property->setAccessible(true);
                $found = true;
                $this->assertEquals($cache, $property->getValue());
                break;
            }
        }
        if (!$found) {
            $this->fail('Failed to assert that setting cache works!');
        }
    }
}

<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Traits;

use Zend\Cache\Storage\StorageInterface as CacheStorageInterface;

/**
 * Add caching ability to types
 */
Trait Cacheable
{
    /**
     * NB This is static - therefore you only need to set it once
     * at the beginning of your program, e.g.
     * $int = new IntType(0);
     * $int->setCache($cache)
     *
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected static $cache;

    /**
     * Set cache storage
     *
     * @param \Zend\Cache\Storage\StorageInterface $cache
     * @return chippyash\Type\Traits\Cacheable Fluent Interface
     */
    public function setCache(CacheStorageInterface $cache)
    {
        self::$cache = $cache;

        return $this;
    }

    /**
     * Set cache item if cache available
     *
     * @param string $key
     * @param mixed $value
     * @return chippyash\Type\Traits\Cacheable Fluent Interface
     */
    protected function setCacheItem($key, $value)
    {
        if (!empty(self::$cache)) {
            if (self::$cache->hasItem($key)) {
                self::$cache->setItem($key, $value);
            } else {
                self::$cache->addItem($key, $value);
            }
        }

        return $this;
    }


    /**
     * Get an item from cache, if available - return null if not found
     *
     * @param string $key
     * @return null|mixed
     */
    protected function getCacheItem($key)
    {
        if (empty(self::$cache)) {
            return null;
        }

        return self::$cache->getItem($key);
    }
}

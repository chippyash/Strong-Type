<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type;

use chippyash\Type\Interfaces\TypeInterface;

/**
 * An abstract PHP type as an object
 */
abstract class AbstractType implements TypeInterface
{
    /**
     * Value of the type
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constructor
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->set($value);
    }

    /**
     * Set the object value
     * Forces type
     *
     * @param mixed $value
     * @return chippyash\Type\AbstractType Fluent Interface
     */
    public function set($value)
    {
        $this->value = $this->typeOf($value);

        return $this;
    }

    /**
     * Get the value of the object typed properly
     *
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Magic invoke method
     * Proxy to get()
     * @see get
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->get();
    }

    /**
     * Magic method - convert to string
     *
     * @return string
     */
    public function __toString()
    {
        $tmp = $this->get();
        return (\is_string($tmp) ? $tmp : (string) $tmp);
    }
  
    /**
     * Return correctly typed value for this type
     *
     * @param mixed $value
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    abstract protected function typeOf($value);
}

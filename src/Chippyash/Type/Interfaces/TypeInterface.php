<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace Chippyash\Type\Interfaces;

/**
 * Interface for Chippyash\Type types
 */
interface TypeInterface
{
    /**
     * Set the object value
     * Forces type
     *
     * @param mixed $value
     * @return \Chippyash\Type\Interfaces\TypeInterface Fluent Interface
     */
    public function set($value);

    /**
     * Get the value of the object as a standard PHP type
     *
     * @return mixed
     */
    public function get();

    /**
     * Magic invoke method
     * Proxy to get()
     * @see get
     *
     * @return mixed
     */
    public function __invoke();

    /**
     * Magic method - convert to string representation
     *
     * @return string
     */
    public function __toString();
}

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

use chippyash\Type\AbstractType;

/**
 * An abstract type that has >1 intrinsic values
 */
abstract class AbstractMultiValueType extends AbstractType
{
    /**
     * Values of the type
     *
     * @var array
     */
    protected $value = [];

    /**
     * Map of value names to $value array position
     * [pos=>[name, class], ...]
     * You need to overide this in child classes and set it
     * 
     * pos refers to the order in which it is expected to be placed in the
     * parameter list in construtor and set methods
     * 
     * name is the name of the value
     * 
     * class is the full class name of the expected value object
     * 
     * @var array
     */
    protected $valueMap = [];
    
    /**
     * Constructor
     * This is variant parameter constructor. The type and number of arguments
     * are determined by the value map
     *
     * @override
     * 
     * @param mixed $value
     */
    public function __construct()
    {
        $this->setFromTypes(func_get_args());
    }
    
    /**
     * Magic method - convert to string
     * You MUST overide this in your child class
     * 
     * @return string
     */
    public function __toString()
    {
        return '';
    }
  
    /**
     * Get the value of the object typed properly as PHP Native type
     *
     * @return mixed
     */    
    public function get()
    {
        return $this->getAsNativeType();
    }
    
    /**
     * This is variant parameter method. The type and number of arguments
     * are determined by the value map
     *
     * @return chippyash\Type\AbstractType Fluent Interface
     */
    public function set($value)
    {
        $this->setFromTypes(func_get_args());
        
        return $this;
    }

    /**
     * Magic clone method
     * Ensure value gets cloned when object is cloned
     */
    public function __clone() {
        foreach ($this->value as &$v) {
            $v = clone $v;
        }
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
     * Return multi value as native PHP type
     * 
     * @return mixed
     */
    abstract protected function getAsNativeType();
    
    /**
     * Not defined for multi value types
     *
     * @param mixed $value
     *
     * @throws \BadMethodCallException
     */
    final protected function typeOf($value)
    {
        throw new \BadMethodCallException('typeOf method not defined for AbstractMultiValueType');
    }
    
    /**
     * Maps values passed in as parameters to constructor and set methods into
     * the value array
     * 
     * @param array $params
     * @throws \InvalidArgumentException
     */
    protected function setFromTypes(array $params)
    {
        if (count($params) != count($this->valueMap)) {
            $n = count($params);
            $e = count($this->valueMap);
            throw new \InvalidArgumentException("Expected {$e} parameters, got {$n}");
        }
        
        foreach ($params as $key=>$value) {
            if (!$value instanceof $this->valueMap[$key]['class']) {
                if (is_object($value)) {
                    $c = get_class($value);
                } else {
                    $c = gettype($value);
                }
                throw new \InvalidArgumentException("Invalid Type ({$c}) at position {$key}");
            }
            $this->value[$this->valueMap[$key]['name']] = clone $value;
        }
        
    }
}

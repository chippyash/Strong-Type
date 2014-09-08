<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * Thanks to Florian Wolters for the inspiration
 * @link http://github.com/FlorianWolters/PHP-Component-Number-Fraction
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Number\Rational;

use chippyash\Type\AbstractMultiValueType;
use chippyash\Type\Number\IntType;
use chippyash\Type\BoolType;
use chippyash\Type\Interfaces\RationalTypeInterface;
use chippyash\Type\Interfaces\NumericTypeInterface;
use chippyash\Type\Number\Complex\ComplexType;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\Rational\RationalType;

/**
 * Abstract rational number type
 */
abstract class AbstractRationalType extends AbstractMultiValueType implements RationalTypeInterface, NumericTypeInterface
{
    /**
     * Do we reduce to lowest form on construct and set?
     * 
     * @var boolean
     */
    protected $reduce = true;
        
    /**
     * map of values for this type
     * @var array
     */
    protected $valueMap = [
        0 => ['name' => 'num', 'class' => 'chippyash\Type\Interfaces\NumericTypeInterface'],
        1 => ['name' => 'den', 'class' => 'chippyash\Type\Interfaces\NumericTypeInterface']
    ];    

    /**
     * Return the number as a Complex number i.e. n+0i
     */
    public function asComplex()
    {
        return new ComplexType(
                new RationalType(clone $this->numerator(), clone $this->denominator()),
                new RationalType(new IntType(0), new IntType(1))
                );
    }

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @returns chippyash\Type\Number\Rational\RationalType
     */
    public function asRational()
    {
        return clone $this;
    }

    /**
     * Return number as an IntType number.
     * Will return floor(n/d)
     *
     * @returns chippyash\Type\Number\IntType
     */
    public function asIntType()
    {
        return new IntType(floor($this->get()));
    }

    /**
     * Return number as a FloatType number.
     *
     * @returns chippyash\Type\Number\FloatType
     */
    public function asFloatType()
    {
        return new FloatType($this->get());
    }

    /**
     * Get the numerator
     * @return mixed
     */
    public function numerator()
    {
        return $this->value['num'];
    }

    /**
     * Get the denominator
     *
     * @return mixed
     */
    public function denominator()
    {
        return $this->value['den'];
    }
    
    /**
     * Return the absolute value of the number
     * 
     * @abstract
     *
     * @returns chippyash\Type\Number\Rational\AbstractRationalType
     */
    abstract public function abs();  
    
    /**
     * Reduce this number to it's lowest form
     * 
     * @abstract
     */
    abstract protected function reduce();

    /**
     * Maps values passed in as parameters to constructor and set methods into
     * the value array
     * 
     * @param array $params
     * @throws \InvalidArgumentException
     */
    protected function setFromTypes(array $params)
    {
        parent::setFromTypes($params);
        
        if ($this->reduce) {
            $this->reduce();
        }        
        
        if ($this->value['den']->get() < 0) {
            //normalise the sign
            $this->value['num']->negate();
            $this->value['den']->negate();
        }
    }
}

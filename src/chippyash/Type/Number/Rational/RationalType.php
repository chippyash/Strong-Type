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

use chippyash\Type\Number\Rational\AbstractRationalType;
use chippyash\Type\Number\IntType;
use chippyash\Type\BoolType;


/**
 * A rational number (i.e a fraction)
 * This is the native PHP type.
 *
 */
class RationalType extends AbstractRationalType
{
    /**
     * map of values for this type
     * @var array
     */
    protected $valueMap = [
        0 => ['name' => 'num', 'class' => 'chippyash\Type\Number\IntType'],
        1 => ['name' => 'den', 'class' => 'chippyash\Type\Number\IntType']
    ];
    
    /**
     * Construct new rational
     * NB. Use the RationalTypeFactory to create rationals from native PHP types
     *
     * @param \chippyash\Type\Number\IntType $num numerator
     * @param \chippyash\Type\Number\IntType $den denominator
     * @param \chippyash\Type\BoolType $reduce -optional: default = true
     */
    public function __construct(IntType $num, IntType $den, BoolType $reduce = null)
    {
        if ($reduce != null) {
            $this->reduce = $reduce();
        }
        parent::__construct($num, $den);
    }
    
    /**
     * Get the basic PHP value of the object type properly
     * In this case, the type is an int or float
     *
     * @return int|float
     */
    public function getAsNativeType()
    {
        if ($this->isInteger()) {
            return intval($this->value['num']->get());
        } else {
            return floatval($this->value['num']->get() / $this->value['den']->get());
        }
    }

    /**
     * Magic method - convert to string
     * Returns "<num>/<den>" or "<num>" if isInteger()
     *
     * @return string
     */
    public function __toString()
    {
        $n = $this->value['num']->get();
        if ($this->isInteger()) {
            return "{$n}";
        } else {
            $d = $this->value['den']->get();
            return "{$n}/{$d}";
        }
    }

    /**
     * Negates the number
     *
     * @returns chippyash\Type\Number\Rational\RationalType Fluent Interface
     */
    public function negate()
    {
        $this->value['num']->negate();

        return $this;
    }

    /**
     * Return the absolute value of the number
     *
     * @returns chippyash\Type\Number\Rational\RationalType
     */
    public function abs()
    {
        return new self($this->value['num']->abs(), $this->value['den']->abs());
    }

    /**
     * Is this Rational an expression of an integer, i.e. n/1
     *
     * @return boolean
     */
    public function isInteger()
    {
        return ($this->value['den']->get() === 1);
    }
      
    /**
     * Reduce this number to it's lowest form
     */
    protected function reduce()
    {
        $gcd = $this->gcd($this->value['num']->get(), $this->value['den']->get());
        if ($gcd > 1) {
            $this->value['num']->set($this->value['num']->get() / $gcd) ;
            $this->value['den']->set($this->value['den']->get() / $gcd);
        }
    }

    /**
     * Return GCD of two numbers
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    private function gcd($a, $b)
    {
        return $b ? $this->gcd($b, $a % $b) : $a;
    }

}

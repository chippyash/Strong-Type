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
 * This is the native PHP type.  If you have GMP installed, consider using
 * RationalGCDType
 *
 */
class RationalType extends AbstractRationalType
{

    /**
     * numerator
     * @var IntType
     */
    protected $num;

    /**
     * denominator
     * @var IntType
     */
    protected $den;

    /**
     * Set values for rational
     *
     * @param \chippyash\Type\Number\IntType $num numerator
     * @param \chippyash\Type\Number\IntType $den denominator
     * @param \chippyash\Type\BoolType $reduce -optional: default = true
     *
     * @return \chippyash\Type\Number\Rational\RationalType Fluent Interface
     */
    public function setFromTypes(IntType $num, IntType $den, BoolType $reduce = null)
    {
        $this->num = clone $num;
        $this->den = clone $den;

        if ($this->den->get() < 0) {
            //normalise the sign
            $this->num->negate();
            $this->den->negate();
        }

        if (empty($reduce) || $reduce->get()) {
            $this->reduce();
        }

        return $this;
    }

    /**
     * Get the numerator
     * @return chippyash\Type\Number\IntType
     */
    public function numerator()
    {
        return $this->num;
    }

    /**
     * Get the denominator
     *
     * @return chippyash\Type\Number\IntType
     */
    public function denominator()
    {
        return $this->den;
    }

    /**
     * Get the basic PHP value of the object type properly
     * In this case, the type is an int or float
     *
     * @return int|float
     */
    public function get()
    {
        if ($this->isInteger()) {
            return intval($this->num->get());
        } else {
            return floatval($this->num->get() / $this->den->get());
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
        $n = $this->num->get();
        if ($this->isInteger()) {
            return "{$n}";
        } else {
            $d = $this->den->get();
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
        $this->num->negate();

        return $this;
    }

    /**
     * Return the absolute value of the number
     *
     * @returns chippyash\Type\Number\Rational\RationalType
     */
    public function abs()
    {
        return new self($this->num->abs(), $this->den->abs());
    }

    /**
     * Is this Rational an expression of an integer, i.e. n/1
     *
     * @return boolean
     */
    public function isInteger()
    {
        return ($this->den->get() === 1);
    }

    /**
     * Reduce this number to it's lowest form
     */
    protected function reduce()
    {
        $gcd = $this->gcd($this->num->get(), $this->den->get());
        if ($gcd > 1) {
            $this->num->set($this->num->get() / $gcd) ;
            $this->den->set($this->den->get() / $gcd);
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

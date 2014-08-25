<?php

/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * Thanks to Florian Wolters for the inspiration
 * @link http://github.com/FlorianWolters/PHP-Component-Number-Fraction
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number\Rational;

use chippyash\Type\Interfaces\GMPInterface;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\IntType;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\GMPIntType;
use chippyash\Type\Number\Complex\GMPComplexType;
use chippyash\Type\BoolType;


/**
 * A rational number (i.e a fraction)
 * This is the GMP type.
 *
 */
class GMPRationalType extends RationalType implements GMPInterface
{

    /**
     * numerator
     * @var GMPIntType
     */
    protected $num;

    /**
     * denominator
     * @var GMPIntType
     */
    protected $den;

    /**
     * Set values for rational
     * Will convert non GMP integer types to GMPIntType
     *
     * @param \chippyash\Type\Number\IntType $num numerator
     * @param \chippyash\Type\Number\IntType $den denominator
     * @param \chippyash\Type\BoolType $reduce -optional: default = true
     *
     * @return \chippyash\Type\Number\Rational\GMPRationalType Fluent Interface
     */
    public function setFromTypes(IntType $num, IntType $den, BoolType $reduce = null)
    {
        if (!$num instanceof GMPIntType) {
            $this->num = new GMPIntType($num());
        } else {
            $this->num = clone $num;
        }
        if (!$den instanceof GMPIntType) {
            $this->den = new GMPIntType($den());
        } else {
            $this->den = clone $den;
        }

        if (gmp_cmp($this->den->gmp(),0) < 0) {
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
     * @return chippyash\Type\Number\GMPIntType
     */
    public function numerator()
    {
        return $this->num;
    }

    /**
     * Get the denominator
     *
     * @return chippyash\Type\Number\GMPIntType
     */
    public function denominator()
    {
        return $this->den;
    }

    /**
     * Get the value of the object typed properly
     * as a PHP Native type
     *
     * @return integer|float
     */
    public function get()
    {
        if ($this->isInteger()) {
            return $this->num->get();
        } else {
            $n = intval(gmp_strval($this->num->gmp()));
            $d = intval(gmp_strval($this->den->gmp()));
            return $n/$d;
        }
    }

    /**
     * Return the value of number as a gmp resource, object or array of same
     *
     * @return array [numerator, denominator]
     */
    public function gmp()
    {
        return [$this->num->gmp(), $this->den->gmp()];
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
        return (gmp_cmp($this->den->gmp(),1) == 0);
    }


    /**
     * Return the number as a GMPComplex number i.e. n+0i
     */
    public function asComplex()
    {
        return new GMPComplexType(
                new self(clone $this->numerator(), clone $this->denominator()),
                new self(new GMPIntType(0), new GMPIntType(1))
                );
    }

    /**
     * Return number as GMPRational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @returns chippyash\Type\Number\Rational\GMPRationalType
     */
    public function asRational()
    {
        return clone $this;
    }

    /**
     * Return number as an GMPIntType number.
     * Will return floor(n/d)
     *
     * @returns chippyash\Type\Number\GMPIntType
     */
    public function asIntType()
    {
        return new GMPIntType(floor($this->get()));
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
     * Return this number ^ $exp
     *
     * @return chippyash\Type\Number\Rational\GMPRationalType
     */
    public function pow(IntType $exp)
    {
        return new self($this->num->pow($exp), $this->den->pow($exp));
    }

    /**
     * Return square root of the number
     *
     * @return chippyash\Type\Number\Rational\GMPRationalType
     */
    public function sqrt()
    {
        return new self($this->num->sqrt(), $this->den->sqrt());
    }

    /**
     * Reduce this number to it's lowest form
     */
    protected function reduce()
    {
        $gcd = gmp_gcd($this->num->gmp(), $this->den->gmp());
        if (gmp_cmp($gcd, 1) > 0) {
            $this->num->set(gmp_div_q($this->num->gmp(), $gcd));
            $this->den->set(gmp_div_q($this->den->gmp(), $gcd));
        }
    }

}

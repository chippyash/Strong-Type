<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number;

use chippyash\Type\AbstractType;
use chippyash\Type\Interfaces\NumericTypeInterface;
use chippyash\Type\Number\Complex\ComplexType;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\Complex\ComplexTypeFactory;

/**
 * Integer Type
 */
class IntType extends AbstractType implements NumericTypeInterface
{

    /**
     * Negates the number
     *
     * @returns chippyash\Type\Number\IntType Fluent Interface
     */
    public function negate()
    {
        $this->value *= -1;

        return $this;
    }

    /**
     * Return the number as a Complex number i.e. n+0i
     *
     * @returns chippyash\Type\Number\Complex\ComplexType
     */
    public function asComplex()
    {
        return new ComplexType(
                new RationalType(
                        clone $this, new static(1)),
                new RationalType(
                        new self(0), new static(1))
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
        return new RationalType(clone $this, new static(1));
    }

    /**
     * Return number as an IntType number.
     *
     * @returns chippyash\Type\Number\IntType
     */
    public function asIntType()
    {
        return clone $this;
    }

    /**
     * Return number as a FloatType number.
     *
     * @returns chippyash\Type\Number\FloatType
     */
    public function asFloatType()
    {
        return new FloatType($this->value);
    }

    /**
     * Return the absolute value of the number
     *
     * @returns chippyash\Type\Number\IntType
     */
    public function abs()
    {
        return new static(abs($this->value));
    }

    /**
     * Return all factors of this number (sorted)
     *
     * @return array [factor,factor, ...]
     */
    public function factors()
    {
        $n = $this->value;
        $limit = floor(sqrt(abs($n)));
        $ret = [];
        for ($x = 1; $x <= $limit; $x++) {
            if ($n % $x == 0) {
                $z = $n / $x;
                $ret[$x] = $x;
                $ret[$z] = $z;
            }
        }
        ksort($ret);
        return array_values($ret);
    }

    /**
     * Return all prime factors of this number
     *
     * Adapted from
     * @link http://www.thatsgeeky.com/2011/03/prime-factoring-with-php/
     *
     * @return array [primeFactor => exponent,...]
     */
    public function primeFactors()
    {
        // max_n = 2^31-1 = 2147483647
        $n = $this->value;
        $d = 2;
        $factors = [];
        $dmax = floor(sqrt($n));
        $sieve = [];
        $sieve = array_fill(1, $dmax, 1);
        do {
            $r = false;
            while ($n % $d == 0) {
                $factors[$d] = (isset($factors[$d]) ? $factors[$d] + 1 : 1);
                $n/=$d;
                $r = true;
            }
            if ($r) {
                $dmax = floor(sqrt($n));
            }
            if ($n > 1) {
                for ($i = $d; $i <= $dmax; $i+=$d) {
                    $sieve[$i] = 0;
                }
                do {
                    $d++;
                } while ($d < $dmax && $sieve[$d] != 1 );
                if ($d > $dmax) {
                    $factors[$n] = (isset($factors[$n]) ? $factors[$n] + 1 : 1);
                }
            }
        } while ($n > 1 && $d <= $dmax);

        return $factors;
    }

    /**
     * Return square root of the number
     *
     * Health warning: square roots of non perfect square numbers are
     * inevitably a compromise and likely to have a margin of error
     * 
     * Also note that trying to get sqrt of a negative integer will result
     * in a complex number
     * @link http://www.regentsprep.org/Regents/math/algtrig/ATO6/SquareRootLes.htm
     *
     * @return chippyash\Type\Number\Rational\RationalType|chippyash\Type\Number\Complex\ComplexType
     */
    public function sqrt()
    {
        if ($this->value >= 0) {
            //return rational number
            return RationalTypeFactory::fromFloat(sqrt($this->value));
        } else {
            //return complex number
            $i = RationalTypeFactory::fromFloat(sqrt(abs($this->value)));
            $r = RationalTypeFactory::create(0);
            return ComplexTypeFactory::create($r, $i);
        }
    }

    protected function typeOf($value)
    {
        return intval($value);
    }

}

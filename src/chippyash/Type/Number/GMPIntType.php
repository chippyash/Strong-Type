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

use chippyash\Type\Interfaces\GMPInterface;
use chippyash\Type\Number\IntType;
use chippyash\Type\Number\Complex\GMPComplexType;
use chippyash\Type\Number\Rational\GMPRationalType;
use chippyash\Type\Traits\GmpTypeCheck;

/**
 * Integer Type - for use with GMP extension
 */
class GMPIntType extends IntType implements GMPInterface
{
    use GmpTypeCheck;

    /**
     * Negates the number
     *
     * @returns chippyash\Type\Number\GMPIntType Fluent Interface
     */
    public function negate()
    {
        $this->value = gmp_neg($this->value);

        return $this;
    }

    /**
     * Return the number as a GMPComplex number i.e. n+0i
     *
     * @returns chippyash\Type\Number\Complex\GMPComplexType
     */
    public function asComplex()
    {
        return new GMPComplexType(
                new GMPRationalType(clone $this, new self(1)),
                new GMPRationalType(new self(0), new self(1))
        );
    }

    /**
     * Return number as GMPRational number.
     * NB, numerator and denominator will be caste as GMPIntTypes
     *
     * @returns chippyash\Type\Number\Rational\GMPRationalType
     */
    public function asRational()
    {
        return new GMPRationalType(clone $this, new self(1));
    }

    /**
     * Return number as an GMPIntType number.
     *
     * @returns chippyash\Type\Number\GMPIntType
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
        return new FloatType(gmp_strval($this->value));
    }

    /**
     * Return the absolute value of the number
     *
     * @returns chippyash\Type\Number\GMPIntType
     */
    public function abs()
    {
        return new self(gmp_abs($this->value));
    }

    /**
     * Return all factors of this number (sorted)
     * Returned factors are strings
     *
     * @return array [factor,factor, ...]
     */
    public function factors()
    {
        $n = $this->cloneValue();
        $limit = gmp_sqrt($n);
        $zero = gmp_init(0);
        $one = gmp_init(1);
        $ret = [];
        for ($x = gmp_init(1); gmp_cmp($x, $limit) <= 0; $x = gmp_add($x, $one)) {
            if (gmp_cmp(gmp_mod($n, $x), $zero) == 0) {
                $z = gmp_strval(gmp_div_q($n, $x));
                $xx = gmp_strval($x);
                $ret[$xx] = $xx;
                $ret[$z] = $z;
            }
        }
        ksort($ret, SORT_NUMERIC);
        return array_values($ret);
    }

    /**
     * Return all prime factors of this number
     *
     * The keys (prime factors) will be strings
     * The values (exponents will be integers
     *
     * Adapted from
     * @link http://www.thatsgeeky.com/2011/03/prime-factoring-with-php/
     *
     * @return array [primeFactor => exponent,...]
     */
    public function primeFactors()
    {
        $n = $this->cloneValue();
        $d = 2;
        $zero = gmp_init(0);
        $one = gmp_init(1);
        $dmax = gmp_sqrt($n);
        $factors = [];
        $sieve = array_fill(1, intval(gmp_strval($dmax)), 1);
        do {
            $r = false;
            while (gmp_cmp(gmp_mod($n, $d), $zero) == 0) {
                $factors[$d] = (isset($factors[$d]) ? $factors[$d] + 1 : 1);
                $n = gmp_div_q($n, $d);
                $r = true;
            }
            if ($r) {
                $dmax = gmp_sqrt($n);
            }
            if (gmp_cmp($n, $one) > 0) {
                for ($i = $d; gmp_cmp($i, $dmax) <= 0; $i+=$d) {
                    $sieve[$i] = 0;
                }
                do {
                    $d++;
                } while (gmp_cmp($d, $dmax) < 0 && $sieve[$d] != 1 );
                if (gmp_cmp($d, $dmax) > 0) {
                    $key = gmp_strval($n);
                    $factors[$key] = (isset($factors[$key]) ? $factors[$key] + 1 : 1);
                }
            }
        } while (gmp_cmp($n, $one) > 0 && gmp_cmp($d, $dmax) <= 0);

        return $factors;
    }

    /**
     * Magic method - convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return gmp_strval($this->value);
    }

    /**
     * Get the value of the object typed properly
     * as a PHP Native type
     *
     * @return integer
     */
    public function get()
    {
        return intval(gmp_strval($this->value));
    }

    /**
     * Return the value of number as a gmp resource or object
     *
     * @return gmp resource|gmp object
     */
    public function gmp()
    {
        return $this->value;
    }

    protected function typeOf($value)
    {
        if ($this->gmpTypeCheck($value)) {
            return $value;
        } else {
            return gmp_init(intval($value));
        }
    }


}

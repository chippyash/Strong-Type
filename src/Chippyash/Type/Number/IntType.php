<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace Chippyash\Type\Number;

use Chippyash\Type\AbstractType;
use Chippyash\Type\Interfaces\NumericTypeInterface;
use Chippyash\Type\Number\Complex\ComplexType;
use Chippyash\Type\Number\Rational\RationalType;

/**
 * Integer Type
 */
class IntType extends AbstractType implements NumericTypeInterface
{

    /**
     * Negates the number
     *
     * @return \Chippyash\Type\Number\IntType Fluent Interface
     */
    public function negate()
    {
        $this->value *= -1;

        return $this;
    }

    /**
     * Return the number as a Complex number i.e. n+0i
     *
     * @return \Chippyash\Type\Number\Complex\ComplexType
     */
    public function asComplex()
    {
        return new ComplexType(
            new RationalType(clone $this, new static(1)),
            new RationalType(new self(0), new static(1))
        );
    }

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    public function asRational()
    {
        return new RationalType(clone $this, new static(1));
    }

    /**
     * Return number as an IntType number.
     *
     * @return \Chippyash\Type\Number\IntType
     */
    public function asIntType()
    {
        return clone $this;
    }

    /**
     * Return number as a FloatType number.
     *
     * @return \Chippyash\Type\Number\FloatType
     */
    public function asFloatType()
    {
        return new FloatType($this->value);
    }

    /**
     * Return the absolute value of the number
     *
     * @return \Chippyash\Type\Number\IntType
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
        $number = $this->value;
        $limit = floor(sqrt(abs($number)));
        $ret = array();
        for ($x = 1; $x <= $limit; $x++) {
            if ($number % $x == 0) {
                $other = intval($number / $x);
                $ret[$x] = $x;
                $ret[$other] = $other;
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
        $number = $this->value;
        $divisor = 2;
        $factors = array();
        $dmax = floor(sqrt($number));
        $sieve = array_fill(1, $dmax, 1);
        do {
            $rFlag = false;
            while ($number % $divisor == 0) {
                $factors[$divisor] = (isset($factors[$divisor]) ? $factors[$divisor] + 1 : 1);
                $number /= $divisor;
                $rFlag = true;
            }
            if ($rFlag) {
                $dmax = floor(sqrt($number));
            }
            if ($number > 1) {
                for ($i = $divisor; $i <= $dmax; $i += $divisor) {
                    $sieve[$i] = 0;
                }
                do {
                    $divisor ++;
                } while ($divisor < $dmax && $sieve[$divisor] != 1 );
                if ($divisor > $dmax) {
                    $factors[$number] = (isset($factors[$number]) ? $factors[$number] + 1 : 1);
                }
            }
        } while ($number > 1 && $divisor <= $dmax);

        return $factors;
    }

    /**
     * Return correctly typed value for this type
     *
     * @param mixed $value
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    protected function typeOf($value)
    {
        return intval($value);
    }
}

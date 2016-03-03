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

use Chippyash\Type\Interfaces\GMPInterface;
use Chippyash\Type\Number\Complex\GMPComplexType;
use Chippyash\Type\Number\Rational\GMPRationalType;
use Chippyash\Type\Exceptions\GmpNotSupportedException;

/**
 * Integer Type - for use with GMP extension
 */
class GMPIntType extends IntType implements GMPInterface
{
    protected static $gmpInstalled;
    
    /**
     * Constructor - check for gmp support
     *
     * @param mixed $value
     * @throws GmpNotSupportedException
     */
    public function __construct($value)
    {
        if (empty(self::$gmpInstalled) || $this->checkGmpInstalled()) {
            self::$gmpInstalled = true;
        } else {
            throw new GmpNotSupportedException();
        }
        parent::__construct($value);
    }

    /**
     * Negates the number
     *
     * @return \Chippyash\Type\Number\GMPIntType Fluent Interface
     */
    public function negate()
    {
        $this->value = gmp_neg($this->value);

        return $this;
    }

    /**
     * Return the number as a GMPComplex number i.e. n+0i
     *
     * @return \Chippyash\Type\Number\Complex\GMPComplexType
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
     * @return \Chippyash\Type\Number\Rational\GMPRationalType
     */
    public function asRational()
    {
        return new GMPRationalType(clone $this, new self(1));
    }

    /**
     * Return number as a FloatType number.
     *
     * @return \Chippyash\Type\Number\FloatType
     */
    public function asFloatType()
    {
        return new FloatType(gmp_strval($this->value));
    }

    /**
     * Return the absolute value of the number
     *
     * @return \Chippyash\Type\Number\GMPIntType
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
        $num = $this->cloneValue();
        $limit = gmp_sqrt($num);
        $zero = gmp_init(0);
        $one = gmp_init(1);
        $ret = array();
        for ($x = gmp_init(1); gmp_cmp($x, $limit) <= 0; $x = gmp_add($x, $one)) {
            if (gmp_cmp(gmp_mod($num, $x), $zero) == 0) {
                $xAsInt = gmp_strval($x);
                $other = gmp_strval(gmp_div_q($num, $x));
                $ret[$xAsInt] = $xAsInt;
                $ret[$other] = $other;
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
     * Adapted from http://www.thatsgeeky.com/2011/03/prime-factoring-with-php/
     *
     * @return array [primeFactor => exponent,...]
     */
    public function primeFactors()
    {
        $number = $this->cloneValue();
        $divisor = 2;
        $zero = gmp_init(0);
        $one = gmp_init(1);
        $dmax = gmp_sqrt($number);
        $factors = array();
        $sieve = array_fill(1, intval(gmp_strval($dmax)), 1);
        do {
            $rFlag = false;
            while (gmp_cmp(gmp_mod($number, $divisor), $zero) == 0) {
                $factors[$divisor] = (isset($factors[$divisor]) ? $factors[$divisor] + 1 : 1);
                $number = gmp_div_q($number, $divisor);
                $rFlag = true;
            }
            if ($rFlag) {
                $dmax = gmp_sqrt($number);
            }
            if (gmp_cmp($number, $one) > 0) {
                for ($i = $divisor; gmp_cmp($i, $dmax) <= 0; $i += $divisor) {
                    $sieve[$i] = 0;
                }
                do {
                    $divisor ++;
                } while (gmp_cmp($divisor, $dmax) < 0 && $sieve[$divisor] != 1 );
                if (gmp_cmp($divisor, $dmax) > 0) {
                    $key = gmp_strval($number);
                    $factors[$key] = (isset($factors[$key]) ? $factors[$key] + 1 : 1);
                }
            }
        } while (gmp_cmp($number, $one) > 0 && gmp_cmp($divisor, $dmax) <= 0);

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
     * @return \GMP|\resource resource|gmp object
     */
    public function gmp()
    {
        return $this->value;
    }

    /**
     * Return number as GMPIntType number.
     *
     * @return \Chippyash\Type\Number\GMPIntType
     */
    public function asGMPIntType()
    {
        return clone $this;
    }
    
    /**
     * Return number as IntType
     * 
     * @return \Chippyash\Type\Number\IntType
     */
    public function asIntType()
    {
        return new IntType($this->get());
    }
    
    /**
     * Return the number as a GMPComplex number i.e. n+0i
     * 
     * @return \Chippyash\Type\Number\Complex\GMPComplexType
     */
    public function asGMPComplex()
    {
        return new GMPComplexType(
            new GMPRationalType(new GMPIntType($this->get()), new GMPIntType(1)),
            new GMPRationalType(new GMPIntType(0), new GMPIntType(1))
        );
    }
    
    /**
     * Return number as GMPRational number.
     * NB, numerator and denominator will be caste as GMPIntTypes
     *
     * @return \Chippyash\Type\Number\Rational\GMPRationalType
     */
    public function asGMPRational()
    {
        return new GMPRationalType(new GMPIntType($this->get()), new GMPIntType(1));
    }

    /**
     * Return correctly typed value for this type
     *
     * @param mixed $value
     *
     * @return \GMP|\resource
     */
    protected function typeOf($value)
    {
        if ($this->gmpTypeCheck($value)) {
            return $value;
        } else {
            return gmp_init(intval($value));
        }
    }

    /**
     * @return \GMP|\resource
     */
    protected function cloneValue()
    {
        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            //it's a resource so can be copied
            return $this->value;
        }
        //it's an object so clone
        return clone $this->value;
    }


    /**
     * Check gmp type depending on PHP version
     *
     * @param mixed $value value to check type of
     * @return boolean true if gmp number else false
     */
    protected function gmpTypeCheck($value)
    {
        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            return is_resource($value) && get_resource_type($value) == 'GMP integer';
        }

        return ($value instanceof \GMP);
    }

    /**
     * Is gmp installed?
     *
     * @return boolean
     */
    protected function checkGmpInstalled()
    {
        return extension_loaded('gmp');
    }
}

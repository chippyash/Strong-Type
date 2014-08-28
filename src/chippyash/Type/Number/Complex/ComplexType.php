<?php

/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number\Complex;

use chippyash\Type\Interfaces\ComplexTypeInterface;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Interfaces\NumericTypeInterface;
use chippyash\Type\Exceptions\NotRealComplexException;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\IntType;
use chippyash\Type\Traits\Cacheable;

/**
 * A complex number - algabraic form
 *
 * A complex number is a number that can be expressed in the form a + bi,
 * where a and b are real numbers and i is the imaginary unit,
 * which satisfies the equation i² = −1
 *
 * Complex numbers use real numbers expressed as a RationalType.  This allows
 * for greater arithmetic stability
 *
 * @link http://en.wikipedia.org/wiki/Complex_number
 */
class ComplexType implements ComplexTypeInterface, NumericTypeInterface
{
    use Cacheable;

    /**
     * Real part
     * @var RationalType
     */
    protected $real;

    /**
     * Imaginary part
     * @var RationalType
     */
    protected $imaginary;

    public function __construct(RationalType $real, RationalType $imaginary)
    {
        $this->setFromTypes($real, $imaginary);
    }

    /**
     * Return real part
     * @return RationalType
     */
    public function r()
    {
        return $this->real;
    }

    /**
     * Return imaginary part
     * @return RationalType
     */
    public function i()
    {
        return $this->imaginary;
    }

    /**
     * Is this number equal to zero?
     * @return boolean
     */
    public function isZero()
    {
        return ($this->real->numerator()->get() == 0 && $this->imaginary->numerator()->get() == 0);
    }

    /**
     * Is this number Gaussian, i.e r & i are both equivelent to integers
     *
     * @return boolean
     * @link http://en.wikipedia.org/wiki/Gaussian_integer
     */
    public function isGaussian()
    {
        return ($this->real->denominator()->get() == 1  && $this->imaginary->denominator()->get() == 1);
    }

    /**
     * Return conjugate of this number
     * @return chippyash\Type\Number\Complex\ComplexType
     */
    public function conjugate()
    {
        $r = clone $this->real;
        $i = clone $this->imaginary;
        return new self($r, $i->negate());
    }

    /**
     * Return the modulus, also known as absolute value or magnitude of this number
     * = sqrt(r^2 + i^2);
     *
     * @return \chippyash\Type\Number\Rational\RationalType
     */
    public function modulus()
    {
        if ($this->isReal()) {
            //sqrt(r^2 + 0^2) = sqrt(r^2) = abs(r)
            return $this->real->abs();
        }
        //r^2 & i^2
        $sqrR = ['n'=>pow($this->real->numerator()->get(), 2), 'd'=>pow($this->real->denominator()->get(),2)];
        $sqrI = ['n'=>pow($this->imaginary->numerator()->get(), 2), 'd'=>pow($this->imaginary->denominator()->get(),2)];
        //r^2 + i^2
        $den = $this->lcm($sqrR['d'], $sqrI['d']);
        $num = ($sqrR['n'] * $den / $sqrR['d']) +
               ($sqrI['n'] * $den / $sqrI['d']);

        //sqrt(num/den) = sqrt(num)/sqrt(den)
        //now this a fudge - we ought to be able to get a proper square root using
        //factors etc but what we do instead is to do an approximation by converting
        //to intermediate rationals using as much precision as we can i.e.
        // rN = RationaType(sqrt(num))
        // rD = RationalType(sqrt(den))
        // mod = rN/1 * 1/rD
        $rN = RationalTypeFactory::fromFloat(sqrt($num), 1e-17);
        $rD = RationalTypeFactory::fromFloat(sqrt($den), 1e-17);
        $modN = $rN->numerator()->get() * $rD->denominator()->get();
        $modD = $rN->denominator()->get() * $rD->numerator()->get();

        return RationalTypeFactory::create($modN, $modD);
    }

    /**
     * Return the absolute value of the number
     * Proxy to modulus
     * Required for NumericTypeInterface
     *
     * @returns \chippyash\Type\Number\FloatType
     */
    public function abs()
    {
        return $this->modulus();
    }

    /**
     * Is this number a real number?  i.e. is it in form n+0i
     *
     * @return boolean
     */
    public function isReal()
    {
        return ($this->imaginary->numerator()->get() == 0);
    }

    /**
     * Proxy to get()
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->get();
    }

    /**
     * String representation of complex number
     * If isReal() then string representation of the real part
     * else r(+/-)ii
     *
     * @return string
     */
    public function __toString()
    {
        //zero imaginary part
        if ($this->isReal()) {
            return (string) $this->real;
        }
        
        $i = (string) $this->imaginary;
        
        //zero real part
        if ($this->real->numerator()->get() === 0) {
            return "{$i}i";
        }
        
        //both parts present
        $r = (string) $this->real;
        $i = (string) $this->imaginary;
        $op = ($this->imaginary->numerator()->get() > 0 ? '+' : '');
        
        return "{$r}{$op}{$i}i";
    }

    /**
     * Get PHP native representation.
     * Return float if this isReal() else there isn't one
     * so we'll proxy to __toString
     *
     * @retun string
     */
    public function get()
    {
        if ($this->isReal()) {
            return $this->real->get();
        }
        return $this->__toString();
    }

    /**
     * This extends the chippyash\Type\Interfaces\TypeInterface set method and finds the
     * arguments to satisfy setFromTypes()
     *
     * Expected parameters
     * @see setFromTypes
     *
     * @throws \InvalidArgumentException
     */
    public function set($value)
    {
        if (func_num_args() !== 2) {
            throw new \InvalidArgumentException('set() expects two parameters');
        }

        return $this->setFromTypes(func_get_arg(0), func_get_arg(1));

    }

    /**
     * Set values for complex number
     *
     * @param \chippyash\Type\Number\Rational\RationalType $real real part
     * @param \chippyash\Type\Number\Rational\RationalType $imaginary imaginary part
     *
     * @return chippyash\Type\Number\Complex\ComplexType Fluent Interface
     */
    public function setFromTypes(RationalType $real, RationalType $imaginary)
    {
        $this->real = clone $real;
        $this->imaginary = clone $imaginary;

        return $this;
    }

    /**
     * Negates the number
     *
     * @returns chippyash\Type\Number\Complex\ComplexType Fluent Interface
     */
    public function negate()
    {
        $this->real->negate();
        $this->imaginary->negate();

        return $this;
    }

    /**
     * if this complex number isReal() then return float equivalent
     * else throw an excepton
     *
     * @return float
     *
     * @throws NotRealComplexException
     */
    public function toFloat()
    {
        if ($this->isReal()) {
            return $this->real->get();
        } else {
            throw new NotRealComplexException();
        }
    }

    /**
     * Return the number as a Complex number i.e. a clone of this one
     * Required for NumericTypeInterface
     *
     * @return chippyash\Type\Number\Complex\ComplexType
     */
    public function asComplex()
    {
        return clone $this;
    }

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @returns chippyash\Type\Number\Rational\RationalType
     *
     * @throws NotRealComplexException
     */
    public function asRational()
    {
        if ($this->isReal()) {
            return clone $this->real;
        } else {
            throw new NotRealComplexException();
        }
    }

    /**
     * Return number as an IntType number.
     * If number isReal() will return floor(r())
     *
     * @returns chippyash\Type\Number\IntType
     */
    public function asIntType()
    {
        if ($this->isReal()) {
            return new IntType(floor($this->real->get()));
        } else {
            throw new NotRealComplexException();
        }
    }

    /**
     * Return number as a FloatType number.
     *
     * @returns chippyash\Type\Number\FloatType
     */
    public function asFloatType()
    {
        if ($this->isReal()) {
            return new FloatType($this->real->get());
        } else {
            throw new NotRealComplexException();
        }
    }

    /**
     * Return the angle (sometimes known as the argument) of the number
     * when expressed in polar notation
     * 
     * The return value is a rational expressing theta as radians
     * 
     * @return chippyash\Type\Number\Rational\RationalType
     */
    public function theta()
    {
        return RationalTypeFactory::fromFloat(
                atan2(
                    $this->imaginary->asFloatType()->get(), 
                    $this->real->asFloatType()->get()
                    )
                );
    }
    
    /**
     * Return the radius (soemtimes known as Rho) of the number
     * when expressed in polar notation
     * 
     * @proxy modulus()
     * 
     * @return chippyash\Type\Number\Rational\RationalType
     */
    public function radius()
    {
        return $this->modulus();
    }
    
    /**
     * Returns complex number expressed in polar form
     * 
     * radius == this->modulus()
     * theta is angle expressed in radians
     * 
     * @return array[radius => RationalType, theta => RationalType] 
     */
    public function asPolar()
    {
        return ['radius'=>$this->modulus(), 'theta'=>$this->theta()];
    }
    
    /**
     * Returns the polar quadrant for the complex number
     * Returns 1, 2, 3 or 4 dependent on the quadrant
     * 
     * @return int
     */
    public function polarQuadrant()
    {
        $signR = ($this->real->numerator()->get() > 0 ? '+' : '-');
        $signI = ($this->imaginary->numerator()->get() > 0 ? '+' : '-');
        switch ("{$signR}{$signI}") {
            case '++': return 1;
            case '-+': return 2;
            case '--': return 3;
            case '+-': return 4;
        }
    }
    
    /**
     * Return complex number expressed as a string in polar form
     * i.e. r(cosθ + i⋅sinθ)
     */
    public function polarString()
    {
        if ($this->isZero()) {
            return '0';
        }
        
        $r = $this->checkIntType($this->radius()->asFloatType()->get());
        $t = $this->checkIntType($this->theta()->asFloatType()->get());
        if (is_int($t)) {
            $tpattern = 'cos %1$d + i⋅sin %1$d';
        } else {
            $tpattern = 'cos %1$f + i⋅sin %1$f';
        }
        if ($r == 1) {
            return sprintf($tpattern, $t);
        }
        if (is_int($r)) {
            $rpattern = '%2$d';
        } else {
            $rpattern = '%2$f';
        }
        $pattern = "{$rpattern}({$tpattern})";
        return sprintf($pattern, $t, $r);
    }
    
    private function checkIntType($value)
    {
        $test = intval($value);
        if (($value - $test) == 0) {
            return $test;
        }
        
        return $value;
    }

    /**
     * Return Greatest Common Denominator of two numbers
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    private function gcd($a, $b)
    {
        return $b ? $this->gcd($b, $a % $b) : $a;
    }

    /**
     * Return Least Common Multiple of two numbers
     * @param int $a
     * @param int $b
     * @return int
     */
    private function lcm($a, $b)
    {
        return \abs(($a * $b) / $this->gcd($a, $b));
    }
}

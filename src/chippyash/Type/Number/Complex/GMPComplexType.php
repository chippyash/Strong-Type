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

use chippyash\Type\Number\Complex\ComplexType;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\GMPRationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Exceptions\NotRealComplexException;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\IntType;
use chippyash\Type\Interfaces\GMPInterface;
use chippyash\Type\Number\GMPIntType;

/**
 * A complex number - algabraic form - GMP version
 *
 * A complex number is a number that can be expressed in the form a + bi,
 * where a and b are real numbers and i is the imaginary unit,
 * which satisfies the equation i² = −1
 *
 * Complex numbers use real numbers expressed as a GMPRationalType.  This allows
 * for greater arithmetic stability
 *
 * @link http://en.wikipedia.org/wiki/Complex_number
 */
class GMPComplexType extends ComplexType implements GMPInterface
{

    /**
     * Real part
     * @var GMPRationalType
     */
    protected $real;

    /**
     * Imaginary part
     * @var GMPRationalType
     */
    protected $imaginary;

    public function __construct(GMPRationalType $real, GMPRationalType $imaginary)
    {
        $this->setFromTypes($real, $imaginary);
    }

    /**
     * Return real part
     * @return GMPRationalType
     */
    public function r()
    {
        return $this->real;
    }

    /**
     * Return imaginary part
     * @return GMPRationalType
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
        $zero = gmp_init(0);
        return (gmp_cmp($this->real->numerator()->gmp(), $zero) == 0 &&
                gmp_cmp($this->imaginary->numerator()->gmp(), $zero) == 0);
    }

    /**
     * Is this number Gaussian, i.e r & i are both equivelent to integers
     *
     * @return boolean
     * @link http://en.wikipedia.org/wiki/Gaussian_integer
     */
    public function isGaussian()
    {
        $one = gmp_init(1);
        return (gmp_cmp($this->real->denominator()->gmp(), $one) == 0  &&
                gmp_cmp($this->imaginary->denominator()->gmp(), $one) == 0);
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
     * @return \chippyash\Type\Number\Rational\GMPRationalType
     */
    public function modulus()
    {
        if ($this->isReal()) {
            //sqrt(r^2 + 0^2) = sqrt(r^2) = abs(r)
            return $this->real->abs();
        }
        //get r^2 and i^2
        $sqrR = ['n'=>gmp_pow($this->real->numerator()->gmp(), 2), 'd'=>gmp_pow($this->real->denominator()->gmp(),2)];
        $sqrI = ['n'=>gmp_pow($this->imaginary->numerator()->gmp(), 2), 'd'=>gmp_pow($this->imaginary->denominator()->gmp(),2)];
        //r^2 + i^2
        $den = new GMPIntType($this->lcm($sqrR['d'], $sqrI['d']));
        $num = new GMPIntType(gmp_add(
                gmp_div_q(gmp_mul($sqrR['n'], $den->gmp()), $sqrR['d']),
                gmp_div_q(gmp_mul($sqrI['n'], $den->gmp()), $sqrI['d'])
               ));

        //sqrt(num/den) = sqrt(num)/sqrt(den)
        //now this a fudge - we ought to be able to get a proper square root using
        //factors etc but what we do instead is to do an approximation by converting
        //to intermediate rationals using as much precision as we can i.e.
        // rN = GMPRationaType(sqrt(num))
        // rD = GMPRationalType(sqrt(den))
        // mod = rN/1 * 1/rD
        $rN = RationalTypeFactory::fromFloat(sqrt($num()));
        $rD = RationalTypeFactory::fromFloat(sqrt($den()));
        $modN = gmp_mul($rN->numerator()->gmp(), $rD->denominator()->gmp());
        $modD = gmp_mul($rN->denominator()->gmp(), $rD->denominator()->gmp());

        return new GMPRationalType(new GMPIntType($modN), new GMPIntType($modD));
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
        $im = $this->imaginary->gmp();
        $im0 = gmp_strval($im[0]);
        $im1 = gmp_strval($im[1]);
                
        $zero = new GMPIntType(0);
        return ((gmp_cmp($im[0], $zero->gmp()) == 0));
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
        $r = (string) $this->real;

        if ($this->isReal()) {
            return $r;
        }

        $op = ($this->imaginary->numerator()->get() < 0 ? '' : '+');
        $i = (string) $this->imaginary;

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
     * Will convert non GMP Rationals to GMPRationalType
     *
     * @param \chippyash\Type\Number\Rational\RationalType $real real part
     * @param \chippyash\Type\Number\Rational\RationalType $imaginary imaginary part
     *
     * @return chippyash\Type\Number\Complex\GMPComplexType Fluent Interface
     */
    public function setFromTypes(RationalType $real, RationalType $imaginary)
    {
        if (!$real instanceof GMPRationalType) {
            $this->real = new GMPRationalType(
                    new GMPIntType($real->numerator()->get()),
                    new GMPIntType($real->denominator()->get())
                    );
        } else {
            $this->real = clone $real;
        }
        if (!$imaginary instanceof GMPRationalType) {
            $this->imaginary = new GMPRationalType(
                    new GMPIntType($imaginary->numerator()->get()),
                    new GMPIntType($imaginary->denominator()->get())
                    );
        } else {
            $this->imaginary = clone $imaginary;
        }

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
     * @returns chippyash\Type\Number\Rational\GMPRationalType
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
     * Return number as an GMPIntType number.
     * If number isReal() will return floor(r())
     *
     * @returns chippyash\Type\Number\GMPIntType
     */
    public function asIntType()
    {
        if ($this->isReal()) {
            return new GMPIntType(floor($this->real->get()));
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
     * Return this number ^ $exp
     *
     * @return chippyash\Type\Number\Complex\GMPComplexType
     */
    public function pow(IntType $exp)
    {
        return new self($this->real->pow($exp), $this->imaginary->pow($exp));
    }

    /**
     * Return square root of the number
     *
     * @return chippyash\Type\Number\Complex\GMPComplexType
     */
    public function sqrt()
    {
        return new self($this->real->sqrt(), $this->imaginary->sqrt());
    }

    /**
     * Return the value of number aarray of gmp resources|objects
     *
     * @return gmp array [[num,den],[num,den]]
     */
    public function gmp()
    {
        return [$this->real->gmp(), $this->imaginary->gmp()];
    }

    /**
     * Return Least Common Multiple of two numbers
     * @param int $a
     * @param int $b
     * @return int
     */
    private function lcm($a, $b)
    {
        return gmp_abs(gmp_div_q(gmp_mul($a, $b),gmp_gcd($a, $b)));
    }

}

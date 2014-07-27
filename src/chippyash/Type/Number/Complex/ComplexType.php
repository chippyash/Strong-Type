<?php

/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number\Complex;

use chippyash\Type\Number\Complex\ComplexTypeInterface;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\NumericTypeInterface;
use chippyash\Type\Exceptions\NotRealComplexException;

/**
 * A complex number - algabraic form
 *
 * A complex number is a number that can be expressed in the form a + bi,
 * where a and b are real numbers and i is the imaginary unit,
 * which satisfies the equation i² = −1
 *
 * @link http://en.wikipedia.org/wiki/Complex_number
 */
class ComplexType implements ComplexTypeInterface, NumericTypeInterface
{

    /**
     * Real part
     * @var float
     */
    protected $real;

    /**
     * Imaginary part
     * @var float
     */
    protected $imaginary;

    public function __construct(FloatType $real, FloatType $imaginary)
    {
        $this->setFromTypes($real, $imaginary);
    }

    /**
     * Return real part
     * @return float
     */
    public function r()
    {
        return $this->real;
    }

    /**
     * Return imaginary part
     * @return float
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
        return ($this->real == 0 && $this->imaginary == 0);
    }

    /**
     * Is this number
     * @return boolean
     * @link http://en.wikipedia.org/wiki/Gaussian_integer
     */
    public function isGaussian()
    {
        return (intval($this->real) == $this->real  && intval($this->imaginary) == $this->imaginary);
    }

    /**
     * Return conjugate of this number
     * @return chippyash\Type\Number\Complex\ComplexType
     */
    public function conjugate()
    {
        return new self(new FloatType($this->real), new FloatType($this->imaginary * -1));
    }

    /**
     * Return the modulus, also known as absolute value or magnitude of this number
     * 
     * @return \chippyash\Type\Number\FloatType
     */
    public function modulus()
    {
        return new FloatType(sqrt(pow($this->real, 2)+pow($this->imaginary, 2)));
    }
    
    /**
     * Is this number a real number?  i.e. is it in form n+0i
     * 
     * @return boolean
     */
    public function isReal()
    {
        return ($this->imaginary == 0.0);
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
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isReal()) {
            return "{$this->real}";
        }
        
        $op = ($this->imaginary < 0 ? '' : '+');

        return "{$this->real}{$op}{$this->imaginary}i";
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
            return $this->real;
        }
        return $this->__toString();
    }

    /**
     * This extends the chippyash\Type\TypeInterface set method and finds the
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
     * @param \chippyash\Type\Number\FloatType $real numerator
     * @param \chippyash\Type\Number\FloatType $imaginary denominator
     *
     * @return chippyash\Type\Number\Complex\ComplexTypeInterface Fluent Interface
     */
    public function setFromTypes(FloatType $real, FloatType $imaginary)
    {
        $this->real = $real();
        $this->imaginary = $imaginary();

        return $this;
    }

    /**
     * Negates the number
     * 
     * @returns chippyash\Type\Number\Complex\ComplexType Fluent Interface
     */
    public function negate()
    {
        $this->real *= -1;
        $this->imaginary *= -1;
        
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
            return $this->real;
        } else {
            throw new NotRealComplexException();
        }
    }
}

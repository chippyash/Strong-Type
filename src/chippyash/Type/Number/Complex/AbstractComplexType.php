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

use chippyash\Type\AbstractMultiValueType;
use chippyash\Type\TypeFactory;
use chippyash\Type\Interfaces\ComplexTypeInterface;
use chippyash\Type\Interfaces\NumericTypeInterface;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Exceptions\NotRealComplexException;

/**
 * Abstract complex number type
 */
abstract class AbstractComplexType extends AbstractMultiValueType implements ComplexTypeInterface, NumericTypeInterface
{
    /**
     * map of values for this type
     * @var array
     */
    protected $valueMap = array(
        0 => array('name' => 'real', 'class' => 'chippyash\Type\Interfaces\NumericTypeInterface'),
        1 => array('name' => 'imaginary', 'class' => 'chippyash\Type\Interfaces\NumericTypeInterface')
    );

    /**
     * Return the modulus, also known as absolute value or magnitude of this number
     * = sqrt(r^2 + i^2);
     *
     * @return \chippyash\Type\Number\Rational\RationalType
     */
    abstract public function modulus();
    
    /**
     * Return the angle (sometimes known as the argument) of the number
     * when expressed in polar notation
     * 
     * The return value is a rational expressing theta as radians
     * 
     * @return \chippyash\Type\Number\Rational\RationalType
     */
    abstract public function theta();
    
    
    /**
     * Return complex number expressed as a string in polar form
     * i.e. r(cosθ + i⋅sinθ)
     */
    abstract public function polarString();
        
    /**
     * Return the number as a Complex number i.e. n+0i
     * 
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    abstract public function asComplex();

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @return \chippyash\Type\Number\Rational\RationalType
     *
     * @throws NotRealComplexException
     */
    abstract public function asRational();

    /**
     * Return number as an IntType number.
     * If number isReal() will return floor(r())
     *
     * @return \chippyash\Type\Number\IntType
     * @throws NotRealComplexException
     */
    public function asIntType()
    {
        if ($this->isReal()) {
            return TypeFactory::create('int', floor($this->value['real']->get()));
        } else {
            throw new NotRealComplexException();
        }
    }

    /**
     * Return number as a FloatType number.
     *
     * @return \chippyash\Type\Number\FloatType
     * @throws NotRealComplexException
     */
    public function asFloatType()
    {
        if ($this->isReal()) {
            return TypeFactory::create('float', $this->value['real']->get());
        } else {
            throw new NotRealComplexException();
        }
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
            return $this->value['real']->get();
        } else {
            throw new NotRealComplexException();
        }
    }
    
    /**
     * Return the absolute value of the number
     * 
     * @abstract
     *
     * @return \chippyash\Type\Number\Rational\AbstractRationalType
     */
    public function abs()
    {
        return $this->modulus();
    }
    

    /**
     * Negates the number
     *
     * @return \chippyash\Type\Number\Complex\ComplexType Fluent Interface
     */
    public function negate()
    {
        $this->value['real']->negate();
        $this->value['imaginary']->negate();

        return $this;
    }

    /**
     * Get the real part
     * @return RationalType
     */
    public function r()
    {
        return $this->value['real'];
    }

    /**
     * Get the imaginary part
     *
     * @return RationalType
     */
    public function i()
    {
        return $this->value['imaginary'];
    }
    
    /**
     * Is this number equal to zero?
     * @return boolean
     */
    public function isZero()
    {
        return ($this->value['real']->get() == 0 && $this->value['imaginary']->get() == 0);
    }

    /**
     * Is this number a real number?  i.e. is it in form n+0i
     *
     * @return boolean
     */
    public function isReal()
    {
        return ($this->value['imaginary']->numerator()->get() == 0);
    }
    
    /**
     * Is this number Gaussian, i.e r & i are both equivelent to integers
     *
     * @return boolean
     * @link http://en.wikipedia.org/wiki/Gaussian_integer
     */
    public function isGaussian()
    {
        return ($this->value['real']->denominator()->get() == 1  && 
                $this->value['imaginary']->denominator()->get() == 1);
    }
    
    /**
     * Return conjugate of this number
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    public function conjugate()
    {
        $i = clone $this->value['imaginary'];
        return new static(clone $this->value['real'], $i->negate());
    }
    
    /**
     * Return the radius (sometimes known as Rho) of the number
     * when expressed in polar notation
     * 
     * @return \chippyash\Type\Number\Rational\RationalType
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
        return array('radius'=>$this->modulus(), 'theta'=>$this->theta());
    }
    
    /**
     * Returns the polar quadrant for the complex number
     * Returns 1, 2, 3 or 4 dependent on the quadrant
     * 
     * @return int
     */
    public function polarQuadrant()
    {
        $signR = ($this->value['real']->numerator()->get() > 0 ? '+' : '-');
        $signI = ($this->value['imaginary']->numerator()->get() > 0 ? '+' : '-');
        $ret = 0;
        switch ("{$signR}{$signI}") {
            case '++':
                $ret = 1;
                break;
            case '-+':
                $ret = 2;
                break;
            case '--':
                $ret = 3;
                break;
            case '+-':
                $ret = 4;
                break;
        }

        return $ret;
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
        if ($this->isZero()) {
            return '0';
        }
        
        $r = (string) $this->value['real'];

        if ($this->isReal()) {
            return $r;
        }

        $i = (string) $this->value['imaginary'];
        if ($i[0] != '-') {
            $i = '+' . $i;
        }
        return "{$r}{$i}i";
    }
    
    /**
     * Magic clone method
     * Ensure value gets cloned when object is cloned
     */
    public function __clone() {
        $this->value['real'] = clone $this->value['real'];
        $this->value['imaginary'] = clone $this->value['imaginary'];
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
     * Return multi value as native PHP type
     * 
     * @return float|int|string
     */
    protected function getAsNativeType() {
        if ($this->isZero()) {
            return 0;
        }
        if ($this->isReal()) {
            return $this->value['real']->get();
        }
        //return as string
        return (string) $this;
    }
    
}

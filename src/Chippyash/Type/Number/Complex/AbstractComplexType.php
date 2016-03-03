<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace Chippyash\Type\Number\Complex;

use Chippyash\Type\AbstractMultiValueType;
use Chippyash\Type\TypeFactory;
use Chippyash\Type\Interfaces\ComplexTypeInterface;
use Chippyash\Type\Interfaces\NumericTypeInterface;
use Chippyash\Type\Number\Rational\RationalType;
use Chippyash\Type\Exceptions\NotRealComplexException;

/**
 * Abstract complex number type
 */
abstract class AbstractComplexType extends AbstractMultiValueType implements ComplexTypeInterface, NumericTypeInterface
{
    /**
     * Map of values for this type
     * @var array
     */
    protected $valueMap = array(
        0 => array(
            'name' => 'real',
            'class' => 'Chippyash\Type\Interfaces\NumericTypeInterface'
        ),
        1 => array(
            'name' => 'imaginary',
            'class' => 'Chippyash\Type\Interfaces\NumericTypeInterface'
        )
    );

    /**
     * Return the modulus, also known as absolute value or magnitude of this number
     * = sqrt(r^2 + i^2);
     *
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    abstract public function modulus();
    
    /**
     * Return the angle (sometimes known as the argument) of the number
     * when expressed in polar notation
     * 
     * The return value is a rational expressing theta as radians
     * 
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    abstract public function theta();
    
    /**
     * Return the number as a Complex number i.e. n+0i
     * 
     * @return \Chippyash\Type\Number\Complex\ComplexType
     */
    abstract public function asComplex();

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @return \Chippyash\Type\Number\Rational\RationalType
     *
     * @throws NotRealComplexException
     */
    abstract public function asRational();

    /**
     * Return complex number expressed as a string in polar form
     * i.e. r(cosθ + i⋅sinθ)
     *
     * @return string
     */
    public function polarString()
    {
        if ($this->isZero()) {
            return '0';
        }

        $rho = $this->checkIntType($this->radius()->asFloatType()->get());
        $theta = $this->checkIntType($this->theta()->asFloatType()->get());
        if (is_int($theta)) {
            $tpattern = 'cos %1$d + i⋅sin %1$d';
        } else {
            $tpattern = 'cos %1$f + i⋅sin %1$f';
        }
        if ($rho == 1) {
            return sprintf($tpattern, $theta);
        }
        if (is_int($rho)) {
            $rpattern = '%2$d';
        } else {
            $rpattern = '%2$f';
        }
        $pattern = "{$rpattern}({$tpattern})";
        return sprintf($pattern, $theta, $rho);
    }

    /**
     * Return number as an IntType number.
     * If number isReal() will return floor(r())
     *
     * @return \Chippyash\Type\Number\IntType
     * @throws NotRealComplexException
     */
    public function asIntType()
    {
        if ($this->isReal()) {
            /** @noinspection PhpUndefinedMethodInspection */
            return TypeFactory::create('int', floor($this->value['real']->get()));
        } else {
            throw new NotRealComplexException();
        }
    }

    /**
     * Return number as a FloatType number.
     *
     * @return \Chippyash\Type\Number\FloatType
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
     * If this complex number isReal() then return float equivalent
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
     * @return \Chippyash\Type\Number\Rational\AbstractRationalType
     */
    public function abs()
    {
        return $this->modulus();
    }
    

    /**
     * Negates the number
     *
     * @return \Chippyash\Type\Number\Complex\ComplexType Fluent Interface
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
     * @return \Chippyash\Type\Number\Complex\ComplexType
     */
    public function conjugate()
    {
        $imaginary = clone $this->value['imaginary'];
        return new static(clone $this->value['real'], $imaginary->negate());
    }
    
    /**
     * Return the radius (sometimes known as Rho) of the number
     * when expressed in polar notation
     * 
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    public function radius()
    {
        return $this->modulus();
    }

    /**
     * Returns complex number expressed in polar form
     * 
     * `radius` == this->modulus()
     * `theta` is angle expressed in radians
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
        
        $real = (string) $this->value['real'];

        if ($this->isReal()) {
            return $real;
        }

        $imaginary = (string) $this->value['imaginary'];
        if ($imaginary[0] != '-') {
            $imaginary = '+' . $imaginary;
        }
        return "{$real}{$imaginary}i";
    }
    
    /**
     * Magic clone method
     * Ensure value gets cloned when object is cloned
     *
     * @return void
     */
    public function __clone()
    {
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
    protected function getAsNativeType()
    {
        if ($this->isZero()) {
            return 0;
        }
        if ($this->isReal()) {
            return $this->value['real']->get();
        }
        //return as string
        return (string) $this;
    }

    /**
     * @param $value
     * @return int|float
     */
    protected function checkIntType($value)
    {
        $test = intval($value);
        if (($value - $test) == 0) {
            return $test;
        }

        return $value;
    }
}

<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace Chippyash\Type\Interfaces;

use Chippyash\Type\Number\Rational\RationalType;

/**
 * Interface for Chippyash\Type\Number\Complex\ComplexType types
 * Makes it broadly compatible with other types
 */
interface ComplexTypeInterface extends NumericTypeInterface
{
    /**
     * Get the real part
     * @return RationalType
     */
    public function r();

    /**
     * Get the imaginary part
     *
     * @return RationalType
     */
    public function i();
    
    /**
     * Is this number equal to zero?
     * @return boolean
     */
    public function isZero();

    /**
     * Is this number a real number?  i.e. is it in form n+0i
     *
     * @return boolean
     */
    public function isReal();
    
    /**
     * Is this number Gaussian, i.e r & i are both equivelent to integers
     *
     * @return boolean
     * @link http://en.wikipedia.org/wiki/Gaussian_integer
     */
    public function isGaussian();
    
    /**
     * Return conjugate of this number
     * @return \Chippyash\Type\Number\Complex\ComplexType
     */
    public function conjugate();
    
    /**
     * Return the modulus, also known as absolute value or magnitude of this number
     * = sqrt(r^2 + i^2);
     *
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    public function modulus();
    
    /**
     * Return the angle (sometimes known as the argument) of the number
     * when expressed in polar notation
     * 
     * The return value is a rational expressing theta as radians
     * 
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    public function theta();
    
    /**
     * Return the radius (sometimes known as Rho) of the number
     * when expressed in polar notation
     * 
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    public function radius();
    
    /**
     * Returns complex number expressed in polar form
     * 
     * radius == this->modulus()
     * theta is angle expressed in radians
     * 
     * @return array[radius => RationalType, theta => RationalType] 
     */
    public function asPolar();
    
    /**
     * Returns the polar quadrant for the complex number
     * Returns 1, 2, 3 or 4 dependent on the quadrant
     * 
     * @return int
     */
    public function polarQuadrant();
    
    /**
     * Return complex number expressed as a string in polar form
     * i.e. r(cosθ + i⋅sinθ)
     *
     * @return string
     */
    public function polarString();
}

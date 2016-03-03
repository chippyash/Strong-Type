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

/**
 * Interface for Chippyash\Type GMP types
 */
interface GMPInterface
{
    /**
     * Return the value of number as a gmp resource, object or array
     * May return an array of gmp resource/object
     *
     * @return \GMP|\resource resource|GMP|array
     */
    public function gmp();
    
    /**
     * Return number as GMPIntType number.
     * Will return floor(n/d)
     *
     * @return \Chippyash\Type\Number\GMPIntType
     */
    public function asGMPIntType();
    
    /**
     * Return the number as a GMPComplex number i.e. n+0i
     *
     * @return \Chippyash\Type\Number\Complex\GMPComplexType
     */
    public function asGMPComplex();
    
    /**
     * Return number as GMPRational number.
     * NB, numerator and denominator will be caste as GMPIntTypes
     *
     * @return \Chippyash\Type\Number\Rational\GMPRationalType
     */
    public function asGMPRational();
}

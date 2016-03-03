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
 * A interface to mark numeric types
 */
interface NumericTypeInterface
{

    /**
     * Negates the number
     *
     * @return \Chippyash\Type\Interfaces\NumericTypeInterface Fluent Interface
     */
    public function negate();

    /**
     * Return the number as a Complex number i.e. n+0i
     *
     * @return \Chippyash\Type\Number\Complex\ComplexType
     */
    public function asComplex();

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    public function asRational();

    /**
     * Return number as an IntType number.
     *
     * @return \Chippyash\Type\Number\IntType
     */
    public function asIntType();

    /**
     * Return number as a FloatType number.
     *
     * @return \Chippyash\Type\Number\FloatType
     */
    public function asFloatType();

    /**
     * Return the absolute value of the number
     *
     * @return \Chippyash\Type\Interfaces\NumericTypeInterface
     */
    public function abs();
}

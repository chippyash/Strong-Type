<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Interfaces;

<<<<<<< HEAD
=======
use chippyash\Type\Number\IntType;

>>>>>>> initial commit for gmp support
/**
 * A interface to mark numeric types
 */
interface NumericTypeInterface {

    /**
     * Negates the number
     *
     * @returns chippyash\Type\Interfaces\NumericTypeInterface Fluent Interface
     */
    public function negate();

    /**
     * Return the number as a Complex number i.e. n+0i
     *
     * @return chippyash\Type\Number\Complex\ComplexType
     */
    public function asComplex();

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @returns chippyash\Type\Number\Rational\RationalType
     */
    public function asRational();

    /**
     * Return number as an IntType number.
     *
     * @returns chippyash\Type\Number\IntType
     */
    public function asIntType();

    /**
     * Return number as a FloatType number.
     *
     * @returns chippyash\Type\Number\FloatType
     */
    public function asFloatType();

    /**
     * Return the absolute value of the number
     *
     * @returns chippyash\Type\Interfaces\NumericTypeInterface
     */
    public function abs();
<<<<<<< HEAD
=======

    /**
     * Return this number ^ $exp
     *
     * @return chippyash\Type\Interfaces\NumericTypeInterface
     */
    public function pow(IntType $exp);

    /**
     * Return square root of the number
     *
     * @return chippyash\Type\Number\Rational\RationalType
     */
    public function sqrt();
>>>>>>> initial commit for gmp support
}

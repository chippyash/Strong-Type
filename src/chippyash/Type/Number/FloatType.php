<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number;

use \chippyash\Type\AbstractType;
use chippyash\Type\Interfaces\NumericTypeInterface;
use chippyash\Type\Number\Complex\ComplexType;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;

/**
 * Floating number Type
 */
class FloatType extends AbstractType implements NumericTypeInterface
{

    /**
     * Negates the number
     *
     * @return \chippyash\Type\Number\FloatType Fluent Interface
     */
    public function negate()
    {
        $this->value *= -1;

        return $this;
    }

    /**
     * Return the number as a Complex number i.e. n+0i
     *
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    public function asComplex()
    {
        return new ComplexType(
                $this->asRational(),
                new RationalType(new IntType(0), new IntType(1))
                );
    }

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @return \chippyash\Type\Number\Rational\RationalType
     */
    public function asRational()
    {
        return RationalTypeFactory::fromFloat($this->value);
    }

    /**
     * Return number as an IntType number.
     *
     * @return \chippyash\Type\Number\IntType
     */
    public function asIntType()
    {
        return new IntType($this->value);
    }

    /**
     * Return number as a FloatType number.
     *
     * @return \chippyash\Type\Number\FloatType
     */
    public function asFloatType()
    {
        return clone $this;
    }

    /**
     * Return the absolute value of the number
     *
     * @return \chippyash\Type\Number\FloatType
     */
    public function abs()
    {
        return new self(abs($this->value));
    }

    protected function typeOf($value)
    {
        return floatval($value);
    }
}

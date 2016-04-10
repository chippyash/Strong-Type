<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace Chippyash\Type\Number;

use \Chippyash\Type\AbstractType;
use Chippyash\Type\Interfaces\NumericTypeInterface;
use Chippyash\Type\Number\Complex\ComplexType;
use Chippyash\Type\Number\Rational\RationalType;
use Chippyash\Type\Number\Rational\RationalTypeFactory;

/**
 * Floating number Type
 */
class FloatType extends AbstractType implements NumericTypeInterface
{

    /**
     * Negates the number
     *
     * @return \Chippyash\Type\Number\FloatType Fluent Interface
     */
    public function negate()
    {
        $this->value *= -1;

        return $this;
    }

    /**
     * Return the number as a Complex number i.e. n+0i
     *
     * @return \Chippyash\Type\Number\Complex\ComplexType
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
     * @return \Chippyash\Type\Number\Rational\RationalType
     */
    public function asRational()
    {
        return RationalTypeFactory::fromFloat($this->value);
    }

    /**
     * Return number as an IntType number.
     *
     * @return \Chippyash\Type\Number\IntType
     */
    public function asIntType()
    {
        return new IntType($this->value);
    }

    /**
     * Return number as a FloatType number.
     *
     * @return \Chippyash\Type\Number\FloatType
     */
    public function asFloatType()
    {
        return clone $this;
    }

    /**
     * Return the absolute value of the number
     *
     * @return \Chippyash\Type\Number\FloatType
     */
    public function abs()
    {
        return new self(abs($this->value));
    }

    /**
     * Return the sign of this number
     * -1 if < 0
     * 0 if == 0
     * 1 if > 0
     *
     * @return int
     */
    public function sign()
    {
        return ($this->value < 0 ? -1 : $this->value == 0 ? 0 : 1);
    }

    /**
     * Return correctly typed value for this type
     *
     * @param mixed $value
     *
     * @return float
     */
    protected function typeOf($value)
    {
        return floatval($value);
    }
}

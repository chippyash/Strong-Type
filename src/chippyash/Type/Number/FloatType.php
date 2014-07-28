<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number;

use \chippyash\Type\AbstractType;
use chippyash\Type\Number\NumericTypeInterface;
use chippyash\Type\Number\Complex\ComplexType;

/**
 * Floating number Type
 */
class FloatType extends AbstractType implements NumericTypeInterface
{

    /**
     * Negates the number
     *
     * @returns chippyash\Type\Number\FloatType Fluent Interface
     */
    public function negate()
    {
        $this->value *= -1;

        return $this;
    }

    /**
     * Return the number as a Complex number i.e. n+0i
     */
    public function toComplex()
    {
        return new ComplexType(new self($this->value), new self(0));
    }

    /**
     * Return the absolute value of the number
     *
     * @returns chippyash\Type\Number\FloatType
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

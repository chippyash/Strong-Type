<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Number\Rational;

use chippyash\Type\TypeInterface;
use chippyash\Type\Number\IntType;
use chippyash\Type\BoolType;

/**
 * Interface for chippyash\Type\Number\Rational\RationalType types
 * Makes it broadly compatible with other types
 */
interface RationalTypeInterface extends TypeInterface
{
    /**
     * Set values for rational
     *
     * @param \chippyash\Type\Number\IntType $num numerator
     * @param \chippyash\Type\Number\IntType $den denominator
     * @param \chippyash\Type\BoolType $reduce -optional: default = true
     *
     * @return chippyash\Type\Number\Rational\RationalTypeInterface Fluent Interface
     */
    public function setFromTypes(IntType $num, IntType $den, BoolType $reduce = null);

    /**
     * Get the numerator
     * @return mixed
     */
    public function numerator();

    /**
     * Get the denominator
     *
     * @return mixed
     */
    public function denominator();
}

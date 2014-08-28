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

use chippyash\Type\Interfaces\TypeInterface;
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
     * @return chippyash\Type\Interfaces\RationalTypeInterface Fluent Interface
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

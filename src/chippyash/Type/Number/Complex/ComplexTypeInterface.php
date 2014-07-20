<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Number\Complex;

use chippyash\Type\TypeInterface;
use chippyash\Type\Number\FloatType;

/**
 * Interface for chippyash\Type\Number\Complex\ComplexType types
 * Makes it broadly compatible with other types
 */
interface ComplexTypeInterface extends TypeInterface
{
    /**
     * Set values for complex number
     *
     * @param \chippyash\Type\Number\FloatType $real numerator
     * @param \chippyash\Type\Number\FloatType $imaginary denominator
     *
     * @return chippyash\Type\Number\Complex\ComplexTypeInterface Fluent Interface
     */
    public function setFromTypes(FloatType $real, FloatType $imaginary);

    /**
     * Get the real part
     * @return float
     */
    public function r();

    /**
     * Get the imaginary part
     *
     * @return float
     */
    public function i();

}

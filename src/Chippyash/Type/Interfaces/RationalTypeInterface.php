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
 * Interface for Chippyash\Type\Number\Rational\RationalType types
 * Makes it broadly compatible with other types
 */
interface RationalTypeInterface extends TypeInterface
{
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

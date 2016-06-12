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

use Chippyash\Type\Exceptions\InvalidTypeException;

/**
 * Whole Integer Type
 */
class WholeIntType extends IntType
{

    /**
     * Negates the number
     *
     * @throws \BadMethodCallException
     */
    public function negate()
    {
        throw new \BadMethodCallException('Negate not supported for Whole Int Types');
    }

    /**
     * Return correctly typed value for this type
     *
     * @param mixed $value
     *
     * @return int
     *
     * @throws InvalidTypeException
     */
    protected function typeOf($value)
    {
        $val = intval($value);
        if ($val >- 1) {
            return $val;
        }

        throw new InvalidTypeException("{$val} < 0 for whole integer type");
    }
}

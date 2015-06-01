<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number;

use chippyash\Type\Exceptions\InvalidTypeException;

/**
 * Natural Integer Type
 */
class NaturalIntType extends IntType
{

    /**
     * Negates the number
     * 
     * @throws \BadMethodCallException
     */
    public function negate()
    {
        throw new \BadMethodCallException('Negate not supported for Natural Int Types');
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
        if ($val > 0) {
            return $val;
        } else {
            throw new InvalidTypeException("{$val} < 1 for natural integer type");
        }
    }
}

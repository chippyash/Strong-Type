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

use chippyash\Type\Exceptions\InvalidTypeException;
use chippyash\Type\Number\IntType;

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
            
    protected function typeOf($value)
    {
        $v = intval($value);
        if ($v>-1) {
            return $v;
        } else {
            throw new InvalidTypeException("{$v} < 0 for whole integer type");
        }
    }
}

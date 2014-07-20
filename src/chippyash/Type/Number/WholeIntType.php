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
use chippyash\Type\Exceptions\InvalidTypeException;

/**
 * Whole Integer Type
 */
class WholeIntType extends AbstractType
{

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

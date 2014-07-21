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

/**
 * Floating number Type
 */
class FloatType extends AbstractType implements NumericTypeInterface
{

    protected function typeOf($value)
    {
        return floatval($value);
    }
}

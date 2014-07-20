<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\String;

use \chippyash\Type\AbstractType;

/**
 * String Type
 */
class StringType extends AbstractType
{

    protected function typeOf($value)
    {
        return (string) $value;
    }
}

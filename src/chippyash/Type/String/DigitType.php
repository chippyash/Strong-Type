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

use chippyash\Type\AbstractType;
use Zend\Filter\StaticFilter;

/**
 * Numeric String Type
 *
 */
class DigitType extends AbstractType
{

    /**
     * This will filter out any non numeric characters.  You may potentially
     * get an empty string
     *
     * @param mixed $value
     * @return string
     */
    protected function typeOf($value)
    {
        return StaticFilter::execute($value, 'Digits');
    }
}

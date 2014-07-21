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
use chippyash\Type\Number\NumericTypeInterface;

/**
 * Natural Integer Type
 */
class NaturalIntType extends AbstractType implements NumericTypeInterface
{

    protected function typeOf($value)
    {
        $v = intval($value);
        if ($v>0) {
            return $v;
        } else {
            throw new InvalidTypeException("{$v} < 1 for natural integer type");
        }
    }
}

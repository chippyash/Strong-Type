<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type;

use \chippyash\Type\AbstractType;
/**
 * Boolean Type
 */
class BoolType extends AbstractType
{

    /**
     * Return correctly typed value for this type
     *
     * @param mixed $value
     *
     * @return boolean
     */
    protected function typeOf($value)
    {
        return (boolean) ($value);
    }

    /**
     * Magic method - convert to string
     *
     * @return string
     */
    public function __toString()
    {
        $tmp = $this->get();
        return ($tmp ? 'true' : 'false');
    }
}

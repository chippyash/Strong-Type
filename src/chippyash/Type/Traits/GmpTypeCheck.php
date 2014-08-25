<?php

/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Traits;

/**
 * Check gmp type depending on PHP version
 */
Trait GmpTypeCheck
{

    /**
     * Check gmp type depending on PHP version
     *
     * @param mixed $value value to check type of
     * @return boolean true if gmp number else false
     */
    protected function gmpTypeCheck($value)
    {
        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            return is_resource($value) && get_resource_type($value) == 'GMP integer';
        }

        return ($value instanceof \GMP);
    }

    protected function cloneValue()
    {
        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            //it's a resource so can be copied
            return $this->value;
        }
        //it's an object so clone
        return clone $this->value;
    }

}

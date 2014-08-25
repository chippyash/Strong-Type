<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Interfaces;

/**
 * Interface for chippyash\Type GMP types
 */
interface GMPInterface
{
    /**
     * Return the value of number as a gmp resource, object or array
     * May return an array of gmp resource/object
     *
     * @return gmp resource|GMP|array
     */
    public function gmp();
}

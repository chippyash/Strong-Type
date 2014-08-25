<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Exceptions;

/**
 * Thrown when trying to cast complex to real
 */
class NotRealComplexException extends \Exception {

    protected $msg = 'Not a Real complex type';

    public function __construct($ignored = null, $code = null, $previous = null)
    {
        parent::__construct($this->msg, $code, $previous);
    }

}

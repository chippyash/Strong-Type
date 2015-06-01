<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Exceptions;

/**
 * Invalid type exception
 */
class InvalidTypeException extends \Exception
{

    protected $msg = 'Invalid Type: %s';

    /**
     * @inheritDoc
     * @param string $type Name of invalid type
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($type, $code = null, $previous = null)
    {
        parent::__construct(sprintf($this->msg, $type), $code, $previous);
    }
}

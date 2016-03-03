<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace Chippyash\Type\String;

use Chippyash\Type\AbstractType;
use Chippyash\Zend\ErrorHandler;

/**
 * Numeric String Type
 *
 */
class DigitType extends AbstractType
{

    /**
     * Is PCRE compiled with Unicode support?
     *
     * @var bool
     **/
    protected static $hasPcreUnicodeSupport = null;

    /**
     * This will filter out any non numeric characters.  You may potentially
     * get an empty string
     *
     * @param mixed $value
     * @return string
     */
    protected function typeOf($value)
    {
        return (string) $this->filter($value);
    }

    /**
     * Lifted entirely from the Zend framework so that we don't have to include
     * the Zend\Filter package and all its dependencies.
     *
     * @param  string $value
     * @return string|mixed

     * zendframework/zend-filter/Zend/Filter/Digits.php
     * Zend Framework (http://framework.zend.com/)
     *
     * @link      http://github.com/zendframework/zf2 for the canonical source repository
     * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd New BSD License
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value, removing all but digit characters
     *
     * If the value provided is non-scalar, the value will remain unfiltered
     *
     */
    protected function filter($value)
    {
        if (!is_scalar($value)) {
            return $value;
        }
        $value = (string) $value;

        if (!$this->hasPcreUnicodeSupport()) {
            // POSIX named classes are not supported, use alternative 0-9 match
            $pattern = '/[^0-9]/';
        } elseif (extension_loaded('mbstring')) {
            // Filter for the value with mbstring
            $pattern = '/[^[:digit:]]/';
        } else {
            // Filter for the value without mbstring
            $pattern = '/[\p{^N}]/';
        }

        return preg_replace($pattern, '', $value);
    }

    /**
     * Lifted entirely from Zend Framework (http://framework.zend.com/) so we don't have
     * to include Zend/Stdlib
     *
     * @link      http://github.com/zendframework/zf2 for the canonical source repository
     * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd New BSD License
     * Is PCRE compiled with Unicode support?
     *
     * @return bool
     */
    protected function hasPcreUnicodeSupport()
    {
        if (static::$hasPcreUnicodeSupport === null) {
            ErrorHandler::start();
            static::$hasPcreUnicodeSupport =
                defined('PREG_BAD_UTF8_OFFSET_ERROR') && preg_match('/\pL/u', 'a') == 1;
            ErrorHandler::stop();
        }
        return static::$hasPcreUnicodeSupport;
    }
}

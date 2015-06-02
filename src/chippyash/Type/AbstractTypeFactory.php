<?php
/**
 * Strong-Type
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace chippyash\Type;

/**
 * Abstract base type for factories
 * Handles gmp/native type determination
 */
abstract class AbstractTypeFactory
{
    /**@+
     * Numeric base types
     */
    const TYPE_DEFAULT = 'auto';
    const TYPE_NATIVE = 'native';
    const TYPE_GMP = 'gmp';
    /**@-*/

    /**
     * Client requested numeric base type support
     * @var string
     */
    protected static $supportType = self::TYPE_DEFAULT;

    /**
     * Numeric base types we can support
     * @var array
     */
    protected static $validTypes = array(self::TYPE_DEFAULT, self::TYPE_GMP, self::TYPE_NATIVE);

    /**
     * The actual base type we are going to return
     * @var string
     */
    protected static $requiredType = null;

    /**
     * Set the required number type to return
     * By default this is self::TYPE_DEFAULT  which is 'auto', meaning that
     * the factory will determine if GMP is installed and use that else use
     * PHP native types
     *
     * @param string $requiredType
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function setNumberType($requiredType)
    {
        if (!in_array($requiredType, self::$validTypes)) {
            throw new \InvalidArgumentException("{$requiredType} is not a supported number type");
        }
        if ($requiredType == self::TYPE_GMP && !extension_loaded('gmp')) {
            throw new \InvalidArgumentException('GMP not supported');
        }
        self::$supportType = $requiredType;
    }

    /**
     * Get the required type base to return
     *
     * @return string
     */
    protected static function getRequiredType()
    {
        if (self::$requiredType != null) {
            return self::$requiredType;
        }

        if (self::$supportType == self::TYPE_DEFAULT) {
            if (extension_loaded('gmp')) {
                self::$requiredType = self::TYPE_GMP;
            } else {
                self::$requiredType = self::TYPE_NATIVE;
            }
        } else {
            self::$requiredType = self::$supportType;
        }

        return self::$requiredType;
    }

}

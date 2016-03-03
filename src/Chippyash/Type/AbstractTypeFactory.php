<?php
/**
 * Strong-Type
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Type;

/**
 * Abstract base type for factories
 * Handles gmp/native type determination
 */
abstract class AbstractTypeFactory
{
    /**@+
     * Numeric base types
     * @deprecated
     */
    const TYPE_DEFAULT = 'auto';
    const TYPE_NATIVE = 'native';
    const TYPE_GMP = 'gmp';
    /**@-*/

    /**
     * Set the required number type to return
     * By default this is RequiredType::TYPE_DEFAULT  which is 'auto', meaning that
     * the factory will determine if GMP is installed and use that else use
     * PHP native types
     *
     * @deprecated Use RequiredType
     *
     * @param string $requiredType
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function setNumberType($requiredType)
    {
        RequiredType::getInstance()->set($requiredType);
    }

    /**
     * Get the required type base to return
     *
     * @return string
     */
    protected static function getRequiredType()
    {
        return RequiredType::getInstance()->get();
    }

}

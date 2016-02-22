<?php
/**
 * Strong-Type
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2016, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace chippyash\Type;

/**
 * Class holding required number base type to be used by type factories
 * This is a singleton class. use getInstance()
 */
class RequiredType
{
    /**@+
     * Numeric base types
     */
    const TYPE_DEFAULT = 'auto';
    const TYPE_NATIVE = 'native';
    const TYPE_GMP = 'gmp';
    /**@-*/

    /**
     * Numeric base types we can support
     * @var array
     */
    protected $validTypes = array(self::TYPE_DEFAULT, self::TYPE_GMP, self::TYPE_NATIVE);

    /**
     * @var string
     */
    protected $requiredType = self::TYPE_DEFAULT;

    /**
     * @var RequiredType
     */
    private static $instance;

    /**
     * Return required number base type
     * @return string
     */
    public function get()
    {
        if ($this->requiredType == self::TYPE_DEFAULT) {
            if (extension_loaded('gmp')) {
                $this->requiredType = self::TYPE_GMP;
            } else {
                $this->requiredType = self::TYPE_NATIVE;
            }
        }

        return $this->requiredType;
    }

    /**
     * Set the required number base type
     *
     * @param $requiredType
     *
     * @return $this
     */
    public function set($requiredType)
    {
        if (!in_array($requiredType, $this->validTypes)) {
            throw new \InvalidArgumentException("{$requiredType} is not a supported number type");
        }
        if ($requiredType == self::TYPE_GMP && !extension_loaded('gmp')) {
            throw new \InvalidArgumentException('GMP not supported');
        }

        $this->requiredType = $requiredType;

        return $this;
    }

    /**
     * Get singleton instance
     *
     * @return RequiredType
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Do not allow public construction
     * @see getInstance()
     *
     * RequiredType constructor.
     */
    protected function __construct()
    {
    }
}
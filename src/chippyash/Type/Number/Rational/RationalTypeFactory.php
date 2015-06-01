<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number\Rational;

use chippyash\Type\Exceptions\InvalidTypeException;
use chippyash\Type\Number\IntType;
use chippyash\Type\Number\GMPIntType;
use chippyash\Type\Number\FloatType;

/**
 * Static Factory for creating Rational types
 */
abstract class RationalTypeFactory
{
    /**
     * default error tolerance for fromFloat()
     */
    const CF_DEFAULT_TOLERANCE = 1.e-15;

    const TYPE_DEFAULT = 'auto';
    const TYPE_NATIVE = 'native';
    const TYPE_GMP = 'gmp';
    
    /**
     * Default error tolerance for from float
     *
     * @see setDefaultFromFloatTolerance()
     * @see fromFloat()
     *
     * @var int
     */
    protected static $defaultTolerance = self::CF_DEFAULT_TOLERANCE;

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
     * Rational type factory
     * Construct and return a rational. You can send in
     * - a string conforming to 'n/d'
     * - a float
     * - two ints (numerator, denominator)
     *
     * @param mixed $numerator
     * @param mixed $denominator
     *
     * @return \chippyash\Type\Number\Rational\RationalType|\chippyash\Type\Number\Rational\GMPRationalType
     *
     * @throws InvalidTypeException
     */
    public static function create($numerator, $denominator = null)
    {
        if (is_string($numerator)) {
            return self::fromString($numerator);
        }

        if (is_float($numerator) || $numerator instanceof FloatType) {
            return self::fromFloat($numerator);
        }

        if (is_numeric($numerator) && is_null($denominator)) {
            return self::createCorrectRational($numerator, 1);
        }

        if (is_numeric($numerator) && is_numeric($denominator)) {
            return self::createCorrectRational($numerator, $denominator);
        }

        if (is_numeric($numerator) && $denominator instanceof IntType) {
            return self::createCorrectRational($numerator, $denominator());
        }

        if ($numerator instanceof IntType && $denominator instanceof IntType) {
            return self::createCorrectRational($numerator(), $denominator());
        }

        if ($numerator instanceof IntType && is_null($denominator)) {
            return self::createCorrectRational($numerator(), 1);
        }

        if ($numerator instanceof IntType && is_numeric($denominator)) {
            return self::createCorrectRational($numerator(), $denominator);
        }

        $typeN = gettype($numerator);
        $typeD = gettype($denominator);
        throw new InvalidTypeException("{$typeN}:{$typeD} for Rational type construction");
    }

    /**
     * Create a rational number from a float or FloatType
     * Use Continued Fractions method of determining the rational number
     *
     * @param float|FloatType $float
     * @param float|FloatType $tolerance -
     *  Default is whatever is currently set but normally self::CF_DEFAULT_TOLERANCE
     *
     * @return \chippyash\Type\Number\Rational\RationalType|\chippyash\Type\Number\Rational\GMPRationalType
     *
     * @throws \InvalidArgumentException
     */
    public static function fromFloat($float, $tolerance = null)
    {
        if ($float instanceof FloatType) {
            $float = $float();
        }
        if ($float == 0.0) {
            return new RationalType(new IntType(0), new IntType(1));
        }
        if ($tolerance instanceof FloatType) {
            $tolerance = $tolerance();
        } elseif (is_null($tolerance)) {
            $tolerance = self::$defaultTolerance;
        }

        $negative = ($float < 0);
        if ($negative) {
            $float = abs($float);
        }
        $num1 = 1;
        $num2 = 0;
        $den1 = 0;
        $den2 = 1;
        $oneOver = 1 / $float;
        do {
            $oneOver = 1 / $oneOver;
            $floor = floor($oneOver);
            $aux = $num1;
            $num1 = $floor * $num1 + $num2;
            $num2 = $aux;
            $aux = $den1;
            $den1 = $floor * $den1 + $den2;
            $den2 = $aux;
            $oneOver = $oneOver - $floor;
        } while (abs($float - $num1 / $den1) > $float * $tolerance);

        if ($negative) {
            $num1 *= -1;
        }
        
        return self::createCorrectRational($num1, $den1);
    }

    /**
     * Create a rational number from a string in form 'n/d'
     *
     * Thanks to Florian Wolters where I lifted this from and amended it
     * @link http://github.com/FlorianWolters/PHP-Component-Number-Fraction
     *
     * @param string $string
     *
     * @return \chippyash\Type\Number\Rational\RationalType|\chippyash\Type\Number\Rational\GMPRationalType
     *
     * @throws \InvalidArgumentException
     */
    public static function fromString($string)
    {
        $matches = array();
        $valid = \preg_match(
            '#^(-)? *?(\d+) *?/ *?(-)? *?(\d+)$#', \trim($string), $matches
        );

        if ($valid !== 1) {
            throw new \InvalidArgumentException(
            'The string representation of the rational is invalid.'
            );
        }

        $numerator = $matches[2];
        $denominator = $matches[4];

        if ($matches[1] xor $matches[3]) {
            // There is one '-' sign: therefore the Rational is negative.
            $numerator *= -1;
        }

        return self::createCorrectRational($numerator, $denominator);
    }

    /**
     * Set the default tolerance for all fromFloat() operations
     * N.B. This sets a static so only needs to be done once
     *
     * @param int $tolerance
     *
     * @return void
     */
    public static function setDefaultFromFloatTolerance($tolerance)
    {
        self::$defaultTolerance = $tolerance;
    }
    
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
    
    /**
     * Create and return the correct number type rational
     * 
     * @param int $num
     * @param int $den
     *
     * @return \chippyash\Type\Number\Rational\RationalType|\chippyash\Type\Number\Rational\GMPRationalType
     */
    protected static function createCorrectRational($num, $den)
    {
        if (self::getRequiredType() == self::TYPE_GMP) {
            return new GMPRationalType(new GMPIntType($num), new GMPIntType($den));
        } else {
            return new RationalType(new IntType($num), new IntType($den));
        }
    }
}

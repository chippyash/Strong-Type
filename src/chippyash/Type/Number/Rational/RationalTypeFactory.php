<?php
/*
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
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\Rational\RationalType;

/**
 * Static Factory for creating Rational types
 */
abstract class RationalTypeFactory
{

    /**
     * default error tolerance for fromFloat()
     */
    const CF_DEFAULT_TOLERANCE = 1.e-15;

    /**
     * Default error tolerance for from float
     * @see setDefaultFromFloatTolerance()
     * @see fromFloat()
     *
     * @var int
     */
    protected static $defaultTolerance = self::CF_DEFAULT_TOLERANCE;

    /**
     * Rational type factory
     * Construct and return a rational. You can send in
     * - a string conforming to 'n/d'
     * - a float
     * - two ints (numerator, denominator)
     *
     * @param string|float|int|IntType $numerator
     * @param int $denominator
     *
     * @return \chippyash\Type\Number\Rational\RationalType
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
            return new RationalType(new IntType($numerator),
                    new IntType(1));
        }

        if (is_numeric($numerator) && is_numeric($denominator)) {
            return new RationalType(new IntType($numerator),
                    new IntType($denominator));
        }

        if (is_numeric($numerator) && $denominator instanceof IntType) {
            return new RationalType(new IntType($numerator), $denominator);
        }

        if ($numerator instanceof IntType && $denominator instanceof IntType) {
            return new RationalType($numerator, $denominator);
        }

        if ($numerator instanceof IntType && is_null($denominator)) {
            return new RationalType($numerator, new IntType(1));
        }

        if ($numerator instanceof IntType && is_numeric($denominator)) {
            return new RationalType($numerator, new IntType($denominator));
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
     * @param float|FloatType $tolerance - Default is whatever is currently set but normally self::CF_DEFAULT_TOLERANCE
     *
     * @return chippyash\Type\Number\Rational\RationalType
     *
     * @throws InvalidArgumentException
     */
    public static function fromFloat($float,
            $tolerance = null)
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
        $n1 = 1;
        $n2 = 0;
        $d1 = 0;
        $d2 = 1;
        $b = 1 / $float;
        do {
            $b = 1 / $b;
            $a = floor($b);
            $aux = $n1;
            $n1 = $a * $n1 + $n2;
            $n2 = $aux;
            $aux = $d1;
            $d1 = $a * $d1 + $d2;
            $d2 = $aux;
            $b = $b - $a;
        } while (abs($float - $n1 / $d1) > $float * $tolerance);

        if ($negative) {
            $n1 *= -1;
        }

        return new RationalType(new IntType($n1), new IntType($d1));
    }

    /**
     * Create a rational number from a string in form 'n/d'
     *
     * Thanks to Florian Wolters where I lifted this from and amended it
     * @link http://github.com/FlorianWolters/PHP-Component-Number-Fraction
     *
     * @param string $string
     *
     * @return chippyash\Type\Number\Rational\RationalType
     *
     * @throws InvalidArgumentException
     */
    public static function fromString($string)
    {
        $matches = [];
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

        return new RationalType(new IntType($numerator),
                new IntType($denominator));
    }

    /**
     * Set the default tolerance for all fromFloat() operations
     * N.B. This sets a static so only needs to be done once
     *
     * @param int $tolerance
     */
    public static function setDefaultFromFloatTolerance($tolerance)
    {
        self::$defaultTolerance = $tolerance;
    }
}

<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number\Complex;

use chippyash\Type\Exceptions\InvalidTypeException;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\IntType;
use chippyash\Type\Number\Complex\ComplexType;

/**
 * Static Factory for creating complex types
 *
 * A complex number is a number that can be expressed in the form a + bi,
 * where a and b are real numbers and i is the imaginary unit,
 * which satisfies the equation i² = −1
 */
abstract class ComplexTypeFactory
{
    /**
     * Complex type factory
     *
     * Construct and return a complex number. You can send in
     * - a string conforming to 'a+bi', 'a-bi', '-a+bi', '-a-bi' where a & b
     * are integer or float numbers e.g. '-12+0.67i'
     * - mixture of numeric (int,float,'1234' etc), IntType and FloatType corresponding to a & b
     *
     * @param string|float|int|FloatType|IntType $realPart
     * @param float|int|FloatType|IntType $imaginaryPart
     *
     * @return \chippyash\Type\Number\Complex\ComplexType
     *
     * @throws InvalidArgumentException
     */
    public static function create($realPart, $imaginaryPart = null)
    {
        if (is_string($realPart)) {
            return self::fromString($realPart);
        }

        if (is_null($imaginaryPart)) {
            throw new \InvalidArgumentException('Imaginary part may not be null if real part is not a string');
        }

        $r = self::convertType($realPart);
        $i = self::convertType($imaginaryPart);

        return new ComplexType($r, $i);
    }

    /**
     * Create a complex number from a string in form '[+,-]<num>(+,-)<num>i'
     * The trailing 'i' character is required
     * No spaces are allowed in the string
     *
     * @param string $string
     * @return chippyash\Type\Number\Complex\ComplexType
     * @throws InvalidArgumentException
     */
    public static function fromString($string)
    {
        $matches = [];
        $valid = \preg_match(
                '#^([-,\+])?([0-9]*\.?[0-9]*)([-,\+]){1}([0-9]*\.?[0-9]*)i$#', \trim($string), $matches
        );

        if ($valid !== 1) {
            throw new \InvalidArgumentException(
            'The string representation of the complex number is invalid.'
            );
        }

        $re = floatval($matches[2]);
        $im = floatval($matches[4]);

        if ($matches[1] && $matches[1] == '-') {
            $re *= -1;
        }
        if ($matches[3] && $matches[3] == '-') {
            $im *= -1;
        }
        return new ComplexType(
                RationalTypeFactory::fromFloat($re),
                RationalTypeFactory::fromFloat($im));
    }

    /**
     * Create complex type from polar co-ordinates
     * 
     * @param \chippyash\Type\Number\Rational\RationalType $radius
     * @param \chippyash\Type\Number\Rational\RationalType $theta angle expressed in radians
     * 
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    public static function fromPolar(RationalType $radius, RationalType $theta)
    {
        $cos = RationalTypeFactory::fromFloat(cos($theta->asFloatType()->get()));
        $sin = RationalTypeFactory::fromFloat(sin($theta->asFloatType()->get()));
        $r = RationalTypeFactory::create(
                new IntType($radius->numerator()->get() * $cos->numerator()->get()),
                new IntType($radius->denominator()->get() * $cos->denominator()->get())
                );
        $i = RationalTypeFactory::create(
                new IntType($radius->numerator()->get() * $sin->numerator()->get()),
                new IntType($radius->denominator()->get() * $sin->denominator()->get())
                );
        return new ComplexType($r, $i);
    }
    
    /**
     * Convert to RationalType
     *
     * @param mixed $t
     *
     * @return \chippyash\Type\Number\RationalType
     *
     * @throws InvalidTypeException
     */
    protected static function convertType($t)
    {
        if ($t instanceof RationalType) {
            return $t;
        }
        if (is_numeric($t)) {
            if (is_int($t)) {
                return new RationalType(new IntType($t), new IntType(1));
            }
            if (is_float($t)) {
                return RationalTypeFactory::fromFloat($t);
            }
        }
        if ($t instanceof FloatType) {
            return RationalTypeFactory::fromFloat($t());
        }
        if ($t instanceof IntType) {
            return new RationalType(new IntType($t()), new IntType(1));
        }

        $typeT = gettype($t);
        throw new InvalidTypeException("{$typeT} for Complex type construction");
    }
}

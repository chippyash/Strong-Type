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
     * Create a rational number from a string in form '[+,-]<num>(+,-)<num>i'
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

        $re = $matches[2];
        $im = $matches[4];

        if ($matches[1] && $matches[1] == '-') {
            $re *= -1;
        }
        if ($matches[3] && $matches[3] == '-') {
            $im *= -1;
        }

        return new ComplexType(new FloatType($re), new FloatType($im));
    }

    /**
     * Convert to FloatType
     *
     * @param mixed $t
     *
     * @return \chippyash\Type\Number\FloatType
     *
     * @throws InvalidTypeException
     */
    protected static function convertType($t)
    {
        if (is_numeric($t)) {
            $r = new FloatType($t);
        }
        if ($t instanceof FloatType) {
            $r = $t;
        }
        if ($t instanceof IntType) {
            $r = new FloatType($t());
        }
        if (!isset($r)) {
            $typeT = gettype($t);
            throw new InvalidTypeException("{$typeT} for Complex type construction");
        }

        return $r;
    }
}

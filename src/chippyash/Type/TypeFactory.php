<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type;

use chippyash\Type\Exceptions\InvalidTypeException;
use chippyash\Type\String\StringType;
use chippyash\Type\String\DigitType;
use chippyash\Type\Number\IntType;
use chippyash\Type\Number\NaturalIntType;
use chippyash\Type\Number\WholeIntType;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\Complex\ComplexTypeFactory;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\BoolType;

/**
 * Static Factory for creating types
 */
abstract class TypeFactory {

    /**
     * Generic type factory
     *
     * @param string $type
     * @param mixed $value
     * $param mixed $extra required for some types
     *
     * @return \chippyash\Type\AbstractType
     *
     * @throws InvalidTypeException
     */
    public static function create($type, $value, $extra = null)
    {
        $type = strtolower($type);

        switch ($type) {
            case 'int':
            case 'integer':
                return self::createInt($value);
            case 'float':
            case 'double':
                return self::createFloat($value);
            case 'string':
                return self::createString($value);
            case 'bool':
            case 'boolean':
                return self::createBool($value);
            case 'digit':
                return self::createDigit($value);
            case 'natural':
                return self::createNatural($value);
            case 'whole':
                return self::createWhole($value);
            case 'complex':
                return self::createComplex($value, $extra);
            case 'rational':
                return self::createRational($value, $extra);
            default:
                throw new InvalidTypeException($type);
        }
    }

    /**
     * Create an IntType
     *
     * @param mixed $value
     * @return \chippyash\Type\Number\IntType
     */
    public static function createInt($value)
    {
        return new IntType($value);
    }

    /**
     * Create a FloatType
     *
     * @param mixed $value
     * @return \chippyash\Type\Number\FloatType
     */
    public static function createFloat($value)
    {
        return new FloatType($value);
    }

    /**
     * Create a StringType
     *
     * @param mixed $value
     * @return \chippyash\Type\String\NStringType
     */
    public static function createString($value)
    {
        return new StringType($value);
    }

    /**
     * Create a BoolType
     *
     * @param mixed $value
     * @return \chippyash\Type\BoolType
     */
    public static function createBool($value)
    {
        return new BoolType($value);
    }

    /**
     * Create a DigitType
     *
     * @param mixed $value
     * @return \chippyash\Type\String\DigitType
     */
    public static function createDigit($value)
    {
        return new DigitType($value);
    }

    /**
     * Create a whole number
     *
     * @param mixed $value
     * @return \chippyash\Type\Number\WholeIntType
     */
    public static function createWhole($value)
    {
        return new WholeIntType($value);
    }

    /**
     * Create a Natural number
     *
     * @param mixed $value
     * @return \chippyash\Type\Number\NaturalIntType
     */
    public static function createNatural($value)
    {
        return new NaturalIntType($value);
    }

    /**
     * Create a Complex number
     *
     * @param string|numeric|IntType|FloatType $realPart
     * @param numeric|IntType|FloatType $imaginaryPart
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    public static function createComplex($realPart, $imaginaryPart = null)
    {
        return ComplexTypeFactory::create($realPart, $imaginaryPart);
    }

    /**
     * Create a Rational number
     * @see RationalTypeFactory::create
     *
     * @param int|string|float $numerator
     * @param int $denominator
     * @return \chippyash\Type\Number\Rational\RationalType
     */
    public static function createRational($numerator, $denominator = 1)
    {
        //check because the create() method can pass in a null
        $denominator = (is_null($denominator) ? 1 : $denominator);
        return RationalTypeFactory::create($numerator, $denominator);
    }

}

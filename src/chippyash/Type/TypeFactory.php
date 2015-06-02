<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 *
 */
namespace chippyash\Type;

use chippyash\Type\Exceptions\InvalidTypeException;
use chippyash\Type\String\StringType;
use chippyash\Type\String\DigitType;
use chippyash\Type\Number\IntType;
use chippyash\Type\Number\GMPIntType;
use chippyash\Type\Number\NaturalIntType;
use chippyash\Type\Number\WholeIntType;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\Complex\ComplexTypeFactory;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Interfaces\NumericTypeInterface;

/**
 * Static Factory for creating types
 */
abstract class TypeFactory extends AbstractTypeFactory
{
    /**
     * Generic type factory
     *
     * @param string $type
     * @param mixed $value
     * @param mixed $extra required for some types
     *
     * @return \chippyash\Type\AbstractType
     *
     * @throws InvalidTypeException
     */
    public static function create($type, $value, $extra = null)
    {
        switch (strtolower($type)) {
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
                throw new InvalidTypeException(strtolower($type));
        }
    }

    /**
     * Create an IntType
     *
     * @param mixed $value
     * @return \chippyash\Type\Number\IntType
     *
     * @throws \InvalidArgumentException
     */
    public static function createInt($value)
    {
        if ($value instanceof NumericTypeInterface) {
            return $value->asIntType();
        }
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("'{$value}' is no valid numeric for IntType");
        }
        if (self::getRequiredType() == self::TYPE_GMP) {
            return new GMPIntType($value);
        } else {
            return new IntType($value);
        }
    }

    /**
     * Create a FloatType
     *
     * @param mixed $value
     * @return \chippyash\Type\Number\FloatType|\chippyash\Type\Number\Rational\GMPRationalType
     *
     * @throws \InvalidArgumentException
     */
    public static function createFloat($value)
    {
        if ($value instanceof NumericTypeInterface) {
            return $value->asFloatType();
        }
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("'{$value}' is no valid numeric for FloatType");
        }
        
        if (self::getRequiredType() == self::TYPE_GMP) {
            return RationalTypeFactory::create($value);
        } else {
            return new FloatType($value);
        }
    }

    /**
     * Create a StringType
     *
     * @param mixed $value
     *
     * @return \chippyash\Type\String\StringType
     */
    public static function createString($value)
    {
        return new StringType($value);
    }

    /**
     * Create a BoolType
     *
     * @param mixed $value
     *
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
     *
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
     *
     * @return \chippyash\Type\Number\WholeIntType|\chippyash\Type\Number\GMPIntType
     *
     * @throws \InvalidArgumentException
     */
    public static function createWhole($value)
    {
        return self::createSuperIntType($value, 'WholeIntType');
    }

    /**
     * Create a Natural number
     *
     * @param mixed $value
     *
     * @return \chippyash\Type\Number\NaturalIntType|\chippyash\Type\Number\GMPIntType
     *
     * @throws \InvalidArgumentException
     */
    public static function createNatural($value)
    {
        return self::createSuperIntType($value, 'NaturalIntType');
    }

    /**
     * Create a Complex number
     * If imaginary part is null, a complex equivalent real number is created r+0i
     *
     * @param int|float|string|NumericTypeInterface $realPart
     * @param int|float|string|NumericTypeInterface|null $imaginaryPart
     *
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    public static function createComplex($realPart, $imaginaryPart = null)
    {
        if ($realPart instanceof NumericTypeInterface) {
            return $realPart->asComplex();
        }
        if (!is_string($realPart) && is_null($imaginaryPart)) {
            return ComplexTypeFactory::create($realPart, 0);
        }
        return ComplexTypeFactory::create($realPart, $imaginaryPart);
    }

    /**
     * Create a Rational number
     * @see RationalTypeFactory::create
     *
     * @param int|string|float $numerator
     * @param int $denominator
     *
     * @return \chippyash\Type\Number\Rational\RationalType
     */
    public static function createRational($numerator, $denominator = 1)
    {
        if ($numerator instanceof NumericTypeInterface) {
            return $numerator->asRational();
        }
        //check because the create() method can pass in a null
        $denominator = (is_null($denominator) ? 1 : $denominator);
        return RationalTypeFactory::create($numerator, $denominator);
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
        parent::setNumberType($requiredType);
        RationalTypeFactory::setNumberType($requiredType);
        ComplexTypeFactory::setNumberType($requiredType);
    }
    
    /**
     * Create a super int type (whole, natural)
     *
     * @param mixed $value
     *
     * @param string $typeClassName
     *
     * @return GMPIntType
     */
    protected static function createSuperIntType($value, $typeClassName)
    {
        if ($value instanceof NumericTypeInterface) {
            $value = $value->asIntType()->get();
        }
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("'{$value}' is no valid numeric for {$typeClassName}");
        }

        if (self::getRequiredType() == self::TYPE_GMP) {
            return new GMPIntType($value);
        } else {
            $nsp = __NAMESPACE__;
            $className = "{$nsp}\\Number\\{$typeClassName}";
            return new $className($value);
        }
    }
}

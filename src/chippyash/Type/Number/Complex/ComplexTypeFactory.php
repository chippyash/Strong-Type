<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number\Complex;

use chippyash\Type\Exceptions\InvalidTypeException;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\GMPRationalType;
use chippyash\Type\Number\Rational\AbstractRationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\IntType;
use chippyash\Type\TypeFactory;

/**
 * Static Factory for creating complex types
 *
 * A complex number is a number that can be expressed in the form a + bi,
 * where a and b are real numbers and i is the imaginary unit,
 * which satisfies the equation i² = −1
 */
abstract class ComplexTypeFactory
{
    const TYPE_DEFAULT = 'auto';
    const TYPE_NATIVE = 'native';
    const TYPE_GMP = 'gmp';
    
    /**
     * Client requested numeric base type support
     * @var string
     */
    protected static $supportType = self::TYPE_DEFAULT;

    /**
     * Numeric base types we can support
     * @var array
     */
    protected static $validTypes = array(
        self::TYPE_DEFAULT,
        self::TYPE_GMP,
        self::TYPE_NATIVE
    );

    /**
     * The actual base type we are going to return
     * @var string
     */
    protected static $requiredType = null;
    
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
     * @throws \InvalidArgumentException
     */
    public static function create($realPart, $imaginaryPart = null)
    {
        if (is_string($realPart)) {
            return self::fromString($realPart);
        }

        if (is_null($imaginaryPart)) {
            throw new \InvalidArgumentException('Imaginary part may not be null if real part is not a string');
        }

        $real = self::convertType($realPart);
        $imaginary = self::convertType($imaginaryPart);
        
        if (self::getRequiredType() == self::TYPE_GMP) {
            return new GMPComplexType($real, $imaginary);
        } else {
            return new ComplexType($real, $imaginary);
        }
    }

    /**
     * Create a complex number from a string in form '[+,-]<num>(+,-)<num>i'
     * The trailing 'i' character is required
     * No spaces are allowed in the string
     *
     * @param string $string
     *
     * @return \chippyash\Type\Number\Complex\ComplexType
     *
     * @throws \InvalidArgumentException
     */
    public static function fromString($string)
    {
        $matches = array();
        $valid = \preg_match(
            '#^([-,\+])?([0-9]*[\.\/]?[0-9]*)([-,\+]){1}([0-9]*[\.\/]?[0-9]*)i$#',
            \trim($string),
            $matches
        );

        if ($valid !== 1) {
            throw new \InvalidArgumentException(
                'The string representation of the complex number is invalid.'
            );
        }

        $real = $matches[2];
        $imaginary = $matches[4];

        if ($matches[1] && $matches[1] == '-') {
            $real = '-' . $real;
        }
        if ($matches[3] && $matches[3] == '-') {
            $imaginary = '-' . $imaginary;
        }
        
        return self::create(self::convertType($real), self::convertType($imaginary));
    }

    /**
     * Create complex type from polar co-ordinates
     * 
     * Be aware that you may lose a bit of precision e.g.
     * $c = ComplexTypeFactory::create('2/7+3/4i');
     * $c2 = ComplexTypeFactory::fromPolar($c->radius(), $c->theta());
     * returns 132664833738225/464326918083788+3382204885901775/4509606514535696i
     * which is ~0.2857142857142854066 + ~0.75000000000000066525i
     * whereas the original is 
     * ~0.28571428571428571429 + 0.75i
     * 
     * formula for conversion is z = r(cos(theta) + i.sin(theta))
     * 
     * @param \chippyash\Type\Number\Rational\AbstractRationalType $radius
     * @param \chippyash\Type\Number\Rational\AbstractRationalType $theta angle expressed in radians
     * 
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    public static function fromPolar(AbstractRationalType $radius, AbstractRationalType $theta)
    {
        if (self::getRequiredType() == self::TYPE_GMP) {
            return self::fromGmpPolar($radius, $theta);
        } else {
            return self::fromNativePolar($radius, $theta);
        }
    }
    
    /**
     * Create complex type from polar co-ordinates - Native version
     * 
     * z = radius x (cos(theta) + i.sin(theta))
     *   real = radius x cos(theta)
     *   imag = radius x sin(theta)
     * 
     * @param \chippyash\Type\Number\Rational\RationalType $radius
     * @param \chippyash\Type\Number\Rational\RationalType $theta angle expressed in radians
     * 
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    public static function fromNativePolar(RationalType $radius, RationalType $theta)
    {
        $cos = RationalTypeFactory::fromFloat(cos($theta()));
        $sin = RationalTypeFactory::fromFloat(sin($theta()));

        list($realNumerator, $realDenominator) = self::getRealPartsFromRadiusAndCos($radius, $cos);
        list($imaginaryNumerator, $imaginaryDenominator) = self::getImaginaryPartsFromRadiusAndSin($radius, $sin);

        $realPart = RationalTypeFactory::create($realNumerator, $realDenominator);
        $imaginaryPart = RationalTypeFactory::create($imaginaryNumerator, $imaginaryDenominator);
        
        return new ComplexType($realPart, $imaginaryPart);
    }

    /**
     * Create complex type from polar co-ordinates - GMP version
     * 
     * @param \chippyash\Type\Number\Rational\GMPRationalType $radius
     * @param \chippyash\Type\Number\Rational\GMPRationalType $theta angle expressed in radians
     * 
     * @return \chippyash\Type\Number\Complex\GMPComplexType
     */
    public static function fromGmpPolar(GMPRationalType $radius, GMPRationalType $theta)
    {
        $cos = RationalTypeFactory::fromFloat(cos($theta()));
        $sin = RationalTypeFactory::fromFloat(sin($theta()));

        //real = radius * cos
        $rNum = TypeFactory::create(
            'int',
            gmp_strval(
                gmp_mul(
                    $radius->numerator()->gmp(),
                    $cos->numerator()->gmp()
                )
            )
        );
        $rDen = TypeFactory::create(
            'int',
            gmp_strval(
                gmp_mul(
                    $radius->denominator()->gmp(),
                    $cos->denominator()->gmp()
                )
            )
        );
        //imag = radius * sin
        $iNum = TypeFactory::create(
            'int',
            gmp_strval(
                gmp_mul(
                    $radius->numerator()->gmp(),
                    $sin->numerator()->gmp()
                )
            )
        );
        $iDen = TypeFactory::create(
            'int',
            gmp_strval(
                gmp_mul(
                    $radius->denominator()->gmp(),
                    $sin->denominator()->gmp()
                )
            )
        );

        return new GMPComplexType(
            RationalTypeFactory::create($rNum, $rDen),
            RationalTypeFactory::create($iNum, $iDen)
        );
    }

    /**
     * Convert to RationalType
     *
     * @param mixed $original
     *
     * @return \chippyash\Type\Number\Rational\RationalType|\chippyash\Type\Number\Rational\GMPRationalType
     *
     * @throws InvalidTypeException
     */
    protected static function convertType($original)
    {
        if ($original instanceof AbstractRationalType) {
            return RationalTypeFactory::create(
                $original->numerator()->get(),
                $original->denominator()->get()
            );
        }
        if (is_numeric($original)) {
            if (is_int($original)) {
                return RationalTypeFactory::create($original, 1);
            }
            //default - convert to float
            return RationalTypeFactory::fromFloat(floatval($original));
        }
        if ($original instanceof FloatType) {
            return RationalTypeFactory::fromFloat($original());
        }
        if ($original instanceof IntType) {
            return RationalTypeFactory::create($original, 1);
        }
        if (is_string($original)) {
            try {
                return RationalTypeFactory::fromString($original);
            } catch (\InvalidArgumentException $e) {
                throw new InvalidTypeException("{$original} for Complex type construction");
            }
        }

        $type = gettype($original);
        throw new InvalidTypeException("{$type} for Complex type construction");
    }
    
    /**
     * Set the required number type to return
     * By default this is self::TYPE_DEFAULT  which is 'auto', meaning that
     * the factory will determine if GMP is installed and use that else use 
     * PHP native types
     * 
     * @param string $requiredType
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
     * @param RationalType $radius
     * @param RationalType $cos
     *
     * @return array
     *
     * @throws InvalidTypeException
     */
    private static function getRealPartsFromRadiusAndCos(RationalType $radius, RationalType $cos)
    {
        return array(
            TypeFactory::create('int', $radius->numerator()->get() * $cos->numerator()->get()),
            TypeFactory::create('int', $radius->denominator()->get() * $cos->denominator()->get())
        ) ;
    }

    /**
     * @param RationalType $radius
     * @param RationalType $sin
     *
     * @return array
     *
     * @throws InvalidTypeException
     */
    private static function getImaginaryPartsFromRadiusAndSin(RationalType $radius, RationalType $sin)
    {
        return array(
            TypeFactory::create('int', $radius->numerator()->get() * $sin->numerator()->get()),
            TypeFactory::create('int', $radius->denominator()->get() * $sin->denominator()->get())
        ) ;
    }

}

<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace Chippyash\Type\Number\Complex;

use Chippyash\Type\Number\Rational\RationalType;
use Chippyash\Type\Number\Rational\GMPRationalType;
use Chippyash\Type\Number\Rational\RationalTypeFactory;
use Chippyash\Type\Exceptions\NotRealComplexException;
use Chippyash\Type\Interfaces\GMPInterface;
use Chippyash\Type\Number\GMPIntType;
use Chippyash\Type\Number\FloatType;
use Chippyash\Type\TypeFactory;

/**
 * A complex number - algabraic form - GMP version
 *
 * A complex number is a number that can be expressed in the form a + bi,
 * where a and b are real numbers and i is the imaginary unit,
 * which satisfies the equation i² = −1
 *
 * Complex numbers use real numbers expressed as a GMPRationalType.  This allows
 * for greater arithmetic stability
 *
 * @link http://en.wikipedia.org/wiki/Complex_number
 */
class GMPComplexType extends AbstractComplexType implements GMPInterface
{

    /**
     * Map of values for this type
     * @var array
     */
    protected $valueMap = array(
        0 => array(
            'name' => 'real',
            'class' => 'Chippyash\Type\Number\Rational\GMPRationalType'
        ),
        1 => array(
            'name' => 'imaginary',
            'class' => 'Chippyash\Type\Number\Rational\GMPRationalType'
        )
    );

    /**
     * Constructor
     *
     * @param GMPRationalType $real
     * @param GMPRationalType $imaginary
     */
    public function __construct(GMPRationalType $real, GMPRationalType $imaginary)
    {
        $this->setFromTypes(array($real, $imaginary));
    }

    /**
     * Is this number equal to zero?
     * @return boolean
     */
    public function isZero()
    {
        return (
            gmp_sign($this->value['real']->numerator()->gmp()) == 0
            && gmp_sign($this->value['imaginary']->numerator()->gmp()) == 0
        );
    }

    /**
     * Return the modulus, also known as absolute value or magnitude of this number
     * = sqrt(r^2 + i^2);
     *
     * @return \Chippyash\Type\Number\Rational\GMPRationalType
     */
    public function modulus()
    {
        if ($this->isReal()) {
            //sqrt(r^2 + 0^2) = sqrt(r^2) = abs(r)
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->value['real']->abs();
        }
        //get r^2 and i^2
        $sqrR = array(
            'n' => gmp_pow($this->value['real']->numerator()->gmp(), 2),
            'd' => gmp_pow($this->value['real']->denominator()->gmp(), 2)
        );
        $sqrI = array(
            'n' => gmp_pow($this->value['imaginary']->numerator()->gmp(), 2),
            'd' => gmp_pow($this->value['imaginary']->denominator()->gmp(), 2)
        );
        //r^2 + i^2
        $den = $this->lcm($sqrR['d'], $sqrI['d']);
        $numRaw = gmp_strval(
            gmp_add(
                gmp_div_q(gmp_mul($sqrR['n'], $den), $sqrR['d']),
                gmp_div_q(gmp_mul($sqrI['n'], $den), $sqrI['d'])
            )
        );
        $num = TypeFactory::createInt($numRaw);

        //sqrt(num/den) = sqrt(num)/sqrt(den)
        //now this a fudge - we ought to be able to get a proper square root using
        //factors etc but what we do instead is to do an approximation by converting
        //to intermediate rationals using as much precision as we can i.e.
        // rNum = GMPRationaType(sqrt(num))
        // rDen = GMPRationalType(sqrt(den))
        // mod = rN/1 * 1/rD
        $rNum = RationalTypeFactory::fromFloat(sqrt($num()));
        $rDen = RationalTypeFactory::fromFloat(sqrt(gmp_strval($den)));
        $modN = gmp_mul($rNum->numerator()->gmp(), $rDen->denominator()->gmp());
        $modD = gmp_mul($rNum->denominator()->gmp(), $rDen->numerator()->gmp());

        return RationalTypeFactory::create(
            (int) gmp_strval($modN),
            (int) gmp_strval($modD)
        );
    }

    /**
     * Return the angle (sometimes known as the argument) of the number
     * when expressed in polar notation
     *
     * The return value is a rational expressing theta as radians
     *
     * @todo implement gmp atan2 method
     *
     * @return \Chippyash\Type\Number\Rational\GMPRationalType
     */
    public function theta()
    {
        return RationalTypeFactory::fromFloat(
            atan2(
                $this->value['imaginary']->asFloatType()->get(),
                $this->value['real']->asFloatType()->get()
            )
        );
    }
    
    /**
     * Return the value of number array of gmp resources|objects
     *
     * @return \GMP|\resource array [[num,den],[num,den]]
     */
    public function gmp()
    {
        return array($this->value['real']->gmp(), $this->value['imaginary']->gmp());
    }

    /**
     * Return number as GMPIntType number.
     * If number isReal() will return floor(r())
     *
     * @return \Chippyash\Type\Number\GMPIntType
     * @throws NotRealComplexException
     */
    public function asGMPIntType()
    {
        if ($this->isReal()) {
            return new GMPIntType(floor($this->value['real']->get()));
        }

        throw new NotRealComplexException();
    }
    
    /**
     * Return the number as a GMPComplex number i.e. a+bi
     * Clones self
     *
     * @return \Chippyash\Type\Number\Complex\GMPComplexType
     */
    public function asGMPComplex()
    {
        return clone $this;
    }
    
    /**
     * Return the number as a Complex number i.e. n+0i
     *
     * @return \Chippyash\Type\Number\Complex\ComplexType
     */
    public function asComplex()
    {
        return new ComplexType($this->r()->asRational(), $this->i()->asRational());
    }
    
    /**
     * Return number as GMPRational number.
     * If number isReal() will return GMPRationalType
     * NB, numerator and denominator will be caste as GMPIntTypes
     *
     * @return \Chippyash\Type\Number\Rational\GMPRationalType
     * @throws NotRealComplexException
     */
    public function asGMPRational()
    {
        if ($this->isReal()) {
            return clone $this->value['real'];
        }
        
        throw new NotRealComplexException();
    }
    
    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @return \Chippyash\Type\Number\Rational\RationalType
     *
     * @throws NotRealComplexException
     */
    public function asRational()
    {
        if ($this->isReal()) {
            return new RationalType(
                $this->value['real']->numerator()->asIntType(),
                $this->value['real']->denominator()->asIntType()
            );
        }
        
        throw new NotRealComplexException();
    }
    
    /**
     * Return number as a FloatType number.
     *
     * @return \Chippyash\Type\Number\FloatType
     * @throws NotRealComplexException
     */
    public function asFloatType()
    {
        if ($this->isReal()) {
            return new FloatType($this->value['real']->get());
        }
         
        throw new NotRealComplexException();
    }
    
    /**
     * Return Least Common Multiple of two numbers
     * @param int $a
     * @param int $b
     * @return int
     */
    private function lcm($a, $b)
    {
        return gmp_abs(gmp_div_q(gmp_mul($a, $b), gmp_gcd($a, $b)));
    }
}

<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number\Complex;

use chippyash\Type\Number\Complex\AbstractComplexType;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\GMPRationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Exceptions\NotRealComplexException;
use chippyash\Type\Interfaces\GMPInterface;
use chippyash\Type\Number\GMPIntType;
use chippyash\Type\TypeFactory;

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
     * map of values for this type
     * @var array
     */
    protected $valueMap = [
        0 => ['name' => 'real', 'class' => 'chippyash\Type\Number\Rational\GMPRationalType'],
        1 => ['name' => 'imaginary', 'class' => 'chippyash\Type\Number\Rational\GMPRationalType']
    ];  
    
    public function __construct(GMPRationalType $real, GMPRationalType $imaginary)
    {
        $this->setFromTypes([$real, $imaginary]);
    }

    /**
     * Return the modulus, also known as absolute value or magnitude of this number
     * = sqrt(r^2 + i^2);
     *
     * @return \chippyash\Type\Number\Rational\GMPRationalType
     */
    public function modulus()
    {
        if ($this->isReal()) {
            //sqrt(r^2 + 0^2) = sqrt(r^2) = abs(r)
            return $this->value['real']->abs();
        }
        //get r^2 and i^2
        $sqrR = ['n'=>gmp_pow($this->value['real']->numerator()->gmp(), 2), 'd'=>gmp_pow($this->value['real']->denominator()->gmp(),2)];
        $sqrI = ['n'=>gmp_pow($this->value['imaginary']->numerator()->gmp(), 2), 'd'=>gmp_pow($this->value['imaginary']->denominator()->gmp(),2)];
        //r^2 + i^2
        $den = $this->lcm($sqrR['d'], $sqrI['d']);
        $numRaw = gmp_strval(gmp_add(
                gmp_div_q(gmp_mul($sqrR['n'], $den), $sqrR['d']),
                gmp_div_q(gmp_mul($sqrI['n'], $den), $sqrI['d'])
               ));
        $num = TypeFactory::createInt($numRaw);

        //sqrt(num/den) = sqrt(num)/sqrt(den)
        //now this a fudge - we ought to be able to get a proper square root using
        //factors etc but what we do instead is to do an approximation by converting
        //to intermediate rationals using as much precision as we can i.e.
        // rN = GMPRationaType(sqrt(num))
        // rD = GMPRationalType(sqrt(den))
        // mod = rN/1 * 1/rD
        $rN = RationalTypeFactory::fromFloat(sqrt($num()));
        $rD = RationalTypeFactory::fromFloat(sqrt(gmp_strval($den)));
        $modN = gmp_mul($rN->numerator()->gmp(), $rD->denominator()->gmp());
        $modD = gmp_mul($rN->denominator()->gmp(), $rD->numerator()->gmp());

        return RationalTypeFactory::create((int) gmp_strval($modN), (int) gmp_strval($modD));
    }

    /**
     * Return the angle (sometimes known as the argument) of the number
     * when expressed in polar notation
     * 
     * The return value is a rational expressing theta as radians
     * 
     * @return chippyash\Type\Number\Rational\GMPRationalType
     * @todo implement gmp atan2 method
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
     * Return complex number expressed as a string in polar form
     * i.e. r(cosθ + i⋅sinθ)
     */
    public function polarString()
    {
        if ($this->isZero()) {
            return '0';
        }
        
        $r = $this->checkIntType($this->radius()->asFloatType()->get());
        $t = $this->checkIntType($this->theta()->asFloatType()->get());
        if (is_int($t)) {
            $tpattern = 'cos %1$d + i⋅sin %1$d';
        } else {
            $tpattern = 'cos %1$f + i⋅sin %1$f';
        }
        if ($r == 1) {
            return sprintf($tpattern, $t);
        }
        if (is_int($r)) {
            $rpattern = '%2$d';
        } else {
            $rpattern = '%2$f';
        }
        $pattern = "{$rpattern}({$tpattern})";
        return sprintf($pattern, $t, $r);
    }
    
    /**
     * Return the value of number array of gmp resources|objects
     *
     * @return gmp array [[num,den],[num,den]]
     */
    public function gmp()
    {
        return [$this->value['real']->gmp(), $this->value['imaginary']->gmp()];
    }

    /**
     * Return number as GMPIntType number.
     * If number isReal() will return floor(r())
     *
     * @returns chippyash\Type\Number\GMPIntType
     */
    public function asGMPIntType()
    {
        if ($this->isReal()) {
            return new GMPIntType(floor($this->value['real']->get()));
        } else {
            throw new NotRealComplexException();
        }
    }
    
    /**
     * Return the number as a GMPComplex number i.e. a+bi
     * Clones self
     * 
     * @returns chippyash\Type\Number\Complex\GMPComplexType
     */
    public function asGMPComplex()
    {
        return clone $this;
    }
    
    /**
     * Return the number as a Complex number i.e. n+0i
     * 
     * @returns chippyash\Type\Number\Complex\ComplexType
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
     * @returns chippyash\Type\Number\Rational\GMPRationalType
     */
    public function asGMPRational()
    {
        if ($this->isReal()) {
            return clone $this->value['real'];
        } else {
            throw new NotRealComplexException();
        }
    }
    
    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @returns chippyash\Type\Number\Rational\RationalType
     *
     * @throws NotRealComplexException
     */
    public function asRational()
    {
        if ($this->isReal()) {
            return new RationalType(
                    $this->value['real']->numerator()->asIntType(),
                    $this->value['real']->denominator()->asIntType());
        } else {
            throw new NotRealComplexException();
        }
    }
    
    /**
     * Return Least Common Multiple of two numbers
     * @param int $a
     * @param int $b
     * @return int
     */
    private function lcm($a, $b)
    {
        return gmp_abs(gmp_div_q(gmp_mul($a, $b),gmp_gcd($a, $b)));
    }

    private function checkIntType($value)
    {
        $test = intval($value);
        if (($value - $test) == 0) {
            return $test;
        }
        
        return $value;
    }
}

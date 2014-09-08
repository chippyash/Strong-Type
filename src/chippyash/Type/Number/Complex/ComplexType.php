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

use chippyash\Type\Interfaces\NumericTypeInterface;
use chippyash\Type\Exceptions\NotRealComplexException;
use chippyash\Type\Number\Complex\AbstractComplexType;
use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\IntType;
use chippyash\Type\Traits\Cacheable;

/**
 * A complex number - algabraic form
 *
 * A complex number is a number that can be expressed in the form a + bi,
 * where a and b are real numbers and i is the imaginary unit,
 * which satisfies the equation i² = −1
 *
 * Complex numbers use real numbers expressed as a RationalType.  This allows
 * for greater arithmetic stability
 *
 * @link http://en.wikipedia.org/wiki/Complex_number
 */
class ComplexType extends AbstractComplexType
{
    /**
     * map of values for this type
     * @var array
     */
    protected $valueMap = [
        0 => ['name' => 'real', 'class' => 'chippyash\Type\Number\Rational\RationalType'],
        1 => ['name' => 'imaginary', 'class' => 'chippyash\Type\Number\Rational\RationalType']
    ];  
    
    public function __construct(RationalType $real, RationalType $imaginary)
    {
        $this->setFromTypes([$real, $imaginary]);
    }

    /**
     * Return the modulus, also known as absolute value or magnitude of this number
     * = sqrt(r^2 + i^2);
     *
     * @return \chippyash\Type\Number\Rational\RationalType
     */
    public function modulus()
    {
        if ($this->isReal()) {
            //sqrt(r^2 + 0^2) = sqrt(r^2) = abs(r)
            return $this->value['real']->abs();
        }
        //r^2 & i^2
        $sqrR = ['n'=>pow($this->value['real']->numerator()->get(), 2), 'd'=>pow($this->value['real']->denominator()->get(),2)];
        $sqrI = ['n'=>pow($this->value['imaginary']->numerator()->get(), 2), 'd'=>pow($this->value['imaginary']->denominator()->get(),2)];
        //r^2 + i^2
        $den = $this->lcm($sqrR['d'], $sqrI['d']);
        $num = ($sqrR['n'] * $den / $sqrR['d']) +
               ($sqrI['n'] * $den / $sqrI['d']);

        //sqrt(num/den) = sqrt(num)/sqrt(den)
        //now this a fudge - we ought to be able to get a proper square root using
        //factors etc but what we do instead is to do an approximation by converting
        //to intermediate rationalsi.e.
        // rN = RationaType(sqrt(num))
        // rD = RationalType(sqrt(den))
        // mod = rN/1 * 1/rD
        $rN = RationalTypeFactory::fromFloat(sqrt($num));
        $rD = RationalTypeFactory::fromFloat(sqrt($den));
        $modN = $rN->numerator()->get() * $rD->denominator()->get();
        $modD = $rN->denominator()->get() * $rD->numerator()->get();

        return RationalTypeFactory::create($modN, $modD);
    }

    /**
     * Return the angle (sometimes known as the argument) of the number
     * when expressed in polar notation
     * 
     * The return value is a rational expressing theta as radians
     * 
     * @return chippyash\Type\Number\Rational\RationalType
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

    private function checkIntType($value)
    {
        $test = intval($value);
        if (($value - $test) == 0) {
            return $test;
        }
        
        return $value;
    }

    /**
     * Return Greatest Common Denominator of two numbers
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    private function gcd($a, $b)
    {
        return $b ? $this->gcd($b, $a % $b) : $a;
    }

    /**
     * Return Least Common Multiple of two numbers
     * @param int $a
     * @param int $b
     * @return int
     */
    private function lcm($a, $b)
    {
        return \abs(($a * $b) / $this->gcd($a, $b));
    }
}

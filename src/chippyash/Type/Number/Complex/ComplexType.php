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

use chippyash\Type\Number\Rational\RationalType;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Exceptions\NotRealComplexException;

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
     * Map of values for this type
     * @var array
     */
    protected $valueMap = array(
        0 => array(
            'name' => 'real',
            'class' => 'chippyash\Type\Number\Rational\RationalType'
        ),
        1 => array(
            'name' => 'imaginary',
            'class' => 'chippyash\Type\Number\Rational\RationalType'
        )
    );

    /**
     * Constructor
     *
     * @param RationalType $real
     * @param RationalType $imaginary
     */
    public function __construct(RationalType $real, RationalType $imaginary)
    {
        $this->setFromTypes(array($real, $imaginary));
    }

    /**
     * Return the number as a Complex number i.e. n+0i
     * 
     * @return \chippyash\Type\Number\Complex\ComplexType
     */
    public function asComplex()
    {
        return clone $this;
    }

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @return \chippyash\Type\Number\Rational\RationalType
     *
     * @throws NotRealComplexException
     */
    public function asRational()
    {
        if ($this->isReal()) {
            return clone $this->value['real'];
        } else {
            throw new NotRealComplexException();
        }
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
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->value['real']->abs();
        }
        //r^2 & i^2
        $sqrR = array(
            'n'=>pow($this->value['real']->numerator()->get(), 2),
            'd'=>pow($this->value['real']->denominator()->get(), 2)
        );
        $sqrI = array(
            'n'=>pow($this->value['imaginary']->numerator()->get(), 2),
            'd'=>pow($this->value['imaginary']->denominator()->get(), 2)
        );
        //r^2 + i^2
        $den = $this->lcm($sqrR['d'], $sqrI['d']);
        $num = ($sqrR['n'] * $den / $sqrR['d']) +
               ($sqrI['n'] * $den / $sqrI['d']);

        //sqrt(num/den) = sqrt(num)/sqrt(den)
        //now this a fudge - we ought to be able to get a proper square root using
        //factors etc but what we do instead is to do an approximation by converting
        //to intermediate rationals i.e.
        // rNum = RationaType(sqrt(num))
        // rDen = RationalType(sqrt(den))
        // mod = rN/1 * 1/rD
        $rNum = RationalTypeFactory::fromFloat(sqrt($num));
        $rDen = RationalTypeFactory::fromFloat(sqrt($den));
        $modN = $rNum->numerator()->get() * $rDen->denominator()->get();
        $modD = $rNum->denominator()->get() * $rDen->numerator()->get();

        return RationalTypeFactory::create($modN, $modD);
    }

    /**
     * Return the angle (sometimes known as the argument) of the number
     * when expressed in polar notation
     * 
     * The return value is a rational expressing theta as radians
     * 
     * @return \chippyash\Type\Number\Rational\RationalType
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
     * Return Greatest Common Denominator of two numbers
     *
     * @param int $a
     * @param int $b
     *
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

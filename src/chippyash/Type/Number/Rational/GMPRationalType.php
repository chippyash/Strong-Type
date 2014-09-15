<?php

/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * Thanks to Florian Wolters for the inspiration
 * @link http://github.com/FlorianWolters/PHP-Component-Number-Fraction
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

namespace chippyash\Type\Number\Rational;

use chippyash\Type\Interfaces\GMPInterface;
use chippyash\Type\Number\Rational\AbstractRationalType;
use chippyash\Type\Number\GMPIntType;
use chippyash\Type\Number\Complex\GMPComplexType;
use chippyash\Type\BoolType;
use chippyash\Type\Traits\GmpTypeCheck;

/**
 * A rational number (i.e a fraction)
 * This is the GMP type.
 *
 */
class GMPRationalType extends AbstractRationalType implements GMPInterface
{
    use GmpTypeCheck;
    
    /**
     * map of values for this type
     * @var array
     */
    protected $valueMap = [
        0 => ['name' => 'num', 'class' => 'chippyash\Type\Number\GMPIntType'],
        1 => ['name' => 'den', 'class' => 'chippyash\Type\Number\GMPIntType']
    ];
    
    /**
     * Construct new GMP rational
     *
     * @param GMPIntType $num numerator
     * @param GMPIntType $den denominator
     * @param \chippyash\Type\BoolType $reduce -optional: default = true
     */
    public function __construct(GMPIntType $num, GMPIntType $den, BoolType $reduce = null)
    {
        if ($reduce != null) {
            $this->reduce = $reduce();
        }
        parent::__construct($num, $den);
    }
    
    /**
     * Get the value of the object typed properly
     * as a PHP Native type
     *
     * @return integer|float
     */
    public function get()
    {
        if ($this->isInteger()) {
            return $this->value['num']->get();
        } else {
            $n = intval(gmp_strval($this->value['num']->gmp()));
            $d = intval(gmp_strval($this->value['den']->gmp()));
            return $n/$d;
        }
    }

    /**
     * Return the value of number as a gmp resource, object or array of same
     *
     * @return array [numerator, denominator]
     */
    public function gmp()
    {
        return [$this->value['num']->gmp(), $this->value['den']->gmp()];
    }

    /**
     * Return number as GMPIntType number.
     * Will return floor(n/d)
     *
     * @returns chippyash\Type\Number\GMPIntType
     */
    public function asGMPIntType()
    {
        return new GMPIntType(floor($this->get()));
    }
    
    /**
     * Return the number as a GMPComplex number i.e. n+0i
     * 
     * @returns chippyash\Type\Number\Complex\GMPComplexType
     */
    public function asGMPComplex()
    {
        return new GMPComplexType(
                new GMPRationalType(clone $this->numerator(), clone $this->denominator()),
                new GMPRationalType(new GMPIntType(0), new GMPIntType(1))
                );
    }
    
    /**
     * Return number as GMPRational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @returns chippyash\Type\Number\Rational\GMPRationalType
     */
    public function asGMPRational()
    {
        return clone $this;
    }
    
    /**
     * Return number as native Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @returns chippyash\Type\Number\Rational\RationalType
     */
    public function asRational()
    {
        return new RationalType(
                $this->value['num']->asIntType(), 
                $this->value['den']->asIntType());
    }
    
    /**
     * Reduce this number to it's lowest form
     */
    protected function reduce()
    {
        $gcd = gmp_gcd($this->value['num']->gmp(), $this->value['den']->gmp());
        if (gmp_cmp($gcd, 1) > 0) {
            $this->value['num']->set(gmp_div_q($this->value['num']->gmp(), $gcd));
            $this->value['den']->set(gmp_div_q($this->value['den']->gmp(), $gcd));
        }
    }

}
<?php

/**
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

namespace Chippyash\Type\Number\Rational;

use Chippyash\Type\Number\IntType;
use Chippyash\Type\BoolType;

/**
 * A rational number (i.e a fraction)
 *
 */
class RationalType extends AbstractRationalType
{
    /**
     * Map of values for this type
     * @var array
     */
    protected $valueMap = array(
        0 => array('name' => 'num', 'class' => 'Chippyash\Type\Number\IntType'),
        1 => array('name' => 'den', 'class' => 'Chippyash\Type\Number\IntType')
    );
    
    /**
     * Construct new rational
     * Use the RationalTypeFactory to create rationals from native PHP types
     *
     * @param \Chippyash\Type\Number\IntType $num numerator
     * @param \Chippyash\Type\Number\IntType $den denominator
     * @param \Chippyash\Type\BoolType $reduce -optional: default = true
     */
    public function __construct(IntType $num, IntType $den, BoolType $reduce = null)
    {
        if ($reduce != null) {
            $this->reduce = $reduce();
        }
        parent::__construct($num, $den);
    }

    /**
     * Reduce this number to it's lowest form
     *
     * @return void
     */
    protected function reduce()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $gcd = $this->gcd($this->value['num']->get(), $this->value['den']->get());
        if ($gcd > 1) {
            $this->value['num']->set($this->value['num']->get() / $gcd) ;
            $this->value['den']->set($this->value['den']->get() / $gcd);
        }
    }

    /**
     * Return GCD of two numbers
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
}

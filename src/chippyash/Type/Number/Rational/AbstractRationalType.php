<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * Thanks to Florian Wolters for the inspiration
 * @link http://github.com/FlorianWolters/PHP-Component-Number-Fraction
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Number\Rational;

use chippyash\Type\Number\IntType;
use chippyash\Type\BoolType;
use chippyash\Type\Number\Rational\RationalTypeInterface;
use chippyash\Type\Number\NumericTypeInterface;
use chippyash\Type\Number\Complex\ComplexType;
use chippyash\Type\Number\FloatType;
use chippyash\Type\Number\Rational\RationalType;

/**
 * Abstract rational number type
 * Does not extend AbstractType, as it requires two parts, but follows it closely
 *
 * Using an abstract base as I will implement variants that utilise gmp and bcmath extensions
 */
abstract class AbstractRationalType implements RationalTypeInterface, NumericTypeInterface
{
    /**
     * numerator
     * @var mixed
     */
    protected $num;

    /**
     * denominator
     * @var mixed
     */
    protected $den;

    /**
     * Construct new rational
     * Use the RationalTypeFactory to create rationals from native PHP types
     *
     * @param \chippyash\Type\Number\IntType $num numerator
     * @param \chippyash\Type\Number\IntType $den denominator
     * @param \chippyash\Type\BoolType $reduce -optional: default = true
     */
    public function __construct(IntType $num, IntType $den, BoolType $reduce = null)
    {
        $this->setFromTypes($num, $den, $reduce);
    }

    /**
     * Magic invoke method
     * Proxy to get()
     * @see get
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->get();
    }

    /**
     * This extends the chippyash\Type\TypeInterface set method and finds the
     * arguments to satisfy setFromTypes()
     *
     * Expected parameters
     * @see setFromTypes
     *
     * @throws \InvalidArgumentException
     */
    public function set($value)
    {
        $nArgs = func_num_args();
        if ($nArgs < 2) {
            throw new \InvalidArgumentException('set() expects at least two parameters');
        }
        $args = func_get_args();
        if ($nArgs == 2) {
            return $this->setFromTypes($args[0], $args[1]);
        }

        return  $this->setFromTypes($args[0], $args[1], $args[2]);
    }

    /**
     * Return the number as a Complex number i.e. n+0i
     */
    public function asComplex()
    {
        return new ComplexType(
                new RationalType(clone $this->numerator(), clone $this->denominator()),
                new RationalType(new IntType(0), new IntType(1))
                );
    }

    /**
     * Return number as Rational number.
     * NB, numerator and denominator will be caste as IntTypes
     *
     * @returns chippyash\Type\Number\Rational\RationalType
     */
    public function asRational()
    {
        return clone $this;
    }

    /**
     * Return number as an IntType number.
     * Will return floor(n/d)
     *
     * @returns chippyash\Type\Number\IntType
     */
    public function asIntType()
    {
        return new IntType(floor($this->get()));
    }

    /**
     * Return number as a FloatType number.
     *
     * @returns chippyash\Type\Number\FloatType
     */
    public function asFloatType()
    {
        return new FloatType($this->get());
    }

    /**
     * Set values for rational
     *
     * @param \chippyash\Type\Number\IntType $num numerator
     * @param \chippyash\Type\Number\IntType $den denominator
     * @param \chippyash\Type\BoolType $reduce -optional: default = true
     *
     * @return \chippyash\Type\Number\Rational\AbstractRationalType Fluent Interface
     */
    abstract public function setFromTypes(IntType $num, IntType $den, BoolType $reduce = null);

    /**
     * Get the numerator
     * @return mixed
     */
    abstract public function numerator();

    /**
     * Get the denominator
     *
     * @return mixed
     */
    abstract public function denominator();

    /**
     * Get the basic PHP value of the object type properly
     * In this case, the type is a float
     *
     * @return float
     */
    abstract public function get();

    /**
     * Magic method - convert to string
     * Returns "<num>/<den>"
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Return the absolute value of the number
     *
     * @returns chippyash\Type\Number\Rational\AbstractRationalType
     */
    abstract public function abs();

    /**
     * Reduce this number to it's lowest form
     */
    abstract protected function reduce();

}

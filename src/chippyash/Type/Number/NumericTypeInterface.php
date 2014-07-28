<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2012
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */
namespace chippyash\Type\Number;

/**
 * A interface to mark numeric types
 */
interface NumericTypeInterface {

    /**
     * Negates the number
     *
     * @returns chippyash\Type\Number\NumericTypeInterface Fluent Interface
     */
    public function negate();

    /**
     * Return the number as a Complex number i.e. n+0i
     */
    public function toComplex();
}

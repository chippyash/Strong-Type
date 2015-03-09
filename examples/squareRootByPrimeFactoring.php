<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 * 
 * An example of using the IntType to determine the square root of a number
 * by using the prime factors of the number
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

include "../vendor/autoload.php";

use chippyash\Type\Number\IntType;

//PHP 5.3
//$nn = [];
//for ($x=1; $x<101; $x++) {
//    $nn[] = $x;
//}
//PHP 5.4+
$nn = range(1,100);
$nn[] = 16449741; //just a big number
foreach ($nn as $ni) {
    $n = new IntType($ni);
    echo "n = {$n}\n";

    //find the prime factors
    $pFactors = $n->primeFactors();
    echo "= ";
    $line = "√(";
    foreach ($pFactors as $prime => $exponent) {
        for ($e=0; $e<$exponent; $e++) {
            $line .= "{$prime} x ";
        }
    }
    $line = rtrim($line, 'x ');
    echo "{$line})\n";

    //reduce
    $left = [];
    $right = [];
    foreach ($pFactors as $prime => $exponent) {
        do {
            if ($exponent > 1) {
                $left[] = $prime;
                $exponent -=2;
            }
        } while ($exponent > 1);
        if ($exponent == 1) {
            $right[] = $prime;
        }
    }
    $line = "= ";
    foreach ($left as $prime) {
        $line .= "{$prime} x ";
    }
    $line = rtrim($line, 'x ') . "√(";
    foreach ($right as $prime) {
        $line .= "{$prime} x ";
    }
    $line = rtrim($line, 'x ');
    echo "{$line})\n";

    //final
    $lterm = 1;
    foreach ($left as $prime) {
        $lterm *= $prime;
    }
    $rterm = 1;
    foreach ($right as $prime) {
        $rterm *= $prime;
    }
    $ssign = '√';
    if ($lterm == 1) {
        $lterm = '';
    }
    if ($rterm == 1) {
        $rterm = '';
        $ssign = '';
    }
    echo "= {$lterm}{$ssign}{$rterm}\n\n";
}
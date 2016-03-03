<?php
/**
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * An example of using tuning
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

include "../vendor/autoload.php";

use Chippyash\Type\TypeFactory;
use Chippyash\Type\RequiredType;
use Chippyash\Type\Number\Rational\RationalTypeFactory;

/**
 * Tuning
 * set tolerance for creating rationals from floats - sqrt() function will use it.
 *
 * Try setting this to the PHP int limit i.e. 1e-17.  You will find that some
 * of the square roots cannot be computed because the limits of the mechanism
 * to convert floats to rational numbers busts the available precision and therefore
 * we get into an overflow situation.
 *
 * Setting tolerance to a lower number, say 1e-6, will compute faster but at the
 * expense of accuracy
 */
RationalTypeFactory::setDefaultFromFloatTolerance(1e-15);

/**
 * Set the required number type.  System will automatically use GMP if
 * it is available.  You can force it to use native PHP thus:
 */
RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);

//now create 10000 numbers for the test
//try playing with this figure to see the results
$numbers = [];
for ($x = 1; $x<10001; $x++) {
    $numbers[$x] = TypeFactory::create('int',$x);
}

//create primes
$primes = [];
$start = microtime(true);
foreach ($numbers as $key => $number) {
    $primes[$key] = $number->primeFactors();
}
$end = microtime(true);
$time = $end - $start;
echo "{$time} secs.\n";

echo "And the results were:\n";
foreach ($primes as $key => $res)
{
    $p = array_keys($res);
    $e = array_values($res);
    $factors = "{$key}=>";
    foreach ($p as $k=>$prime) {
        $factors .= "{$prime}:{$e[$k]}, ";
    }
    $factors = rtrim($factors, ' ,');
    echo "[{$factors}]\n";
}

echo "\nscoll up to see the timings\n";
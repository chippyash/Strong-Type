<?php
/*
 * Hard type support
 * For when you absolutely want to know what you are getting
 *
 * An example of using caching and tuning
 *
 * @author Ashley Kitson <akitson@zf4.biz>
 * @copyright Ashley Kitson, UK, 2014
 * @licence GPL V3 or later : http://www.gnu.org/licenses/gpl.html
 */

include "../vendor/autoload.php";

use chippyash\Type\TypeFactory;
use Zend\Cache\Storage\Adapter\Memory;
use chippyash\Type\Number\Rational\RationalTypeFactory;
use chippyash\Type\Number\IntType;

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

//now create 10000 numbers for the test
//try playing with this figure to see the results
$numbers = [];
for ($x = 1; $x<10001; $x++) {
    $numbers[$x] = new IntType($x);
}

//first pass - no cache
$start = microtime(true);
foreach ($numbers as $number) {
    $number->primeFactors();
}
$end = microtime(true);
$time = $end - $start;
echo "No cache: {$time} secs.\n";

//second pass - first time with cache
//we are using a simple non persistent in-memory cache - you could try different types
$cache = new Memory();
TypeFactory::setCache($cache);

//first pass - with cache
$start = microtime(true);
foreach ($numbers as $number) {
    $number->primeFactors();
}
$end = microtime(true);
$time = $end - $start;
echo "Cache - first time: {$time} secs.\n";

//second pass - with cache
$start = microtime(true);
foreach ($numbers as $number) {
    $number->primeFactors();
}
$end = microtime(true);
$time = $end - $start;
echo "Cache - second time: {$time} secs.\n";

echo "And the results were:\n";
foreach ($cache->getIterator() as $key)
{
    $res = $cache->getItem($key);
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
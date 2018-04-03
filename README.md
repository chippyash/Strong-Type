# chippyash/Type

## Quality Assurance

![PHP 5.6](https://img.shields.io/badge/PHP-5.6-blue.svg)
![PHP 7](https://img.shields.io/badge/PHP-7-blue.svg)
[![Build Status](https://travis-ci.org/chippyash/Strong-Type.svg?branch=master)](https://travis-ci.org/chippyash/Strong-Type)
[![Test Coverage](https://codeclimate.com/github/chippyash/Strong-Type/badges/coverage.svg)](https://codeclimate.com/github/chippyash/Strong-Type/coverage)
[![Code Climate](https://codeclimate.com/github/chippyash/Strong-Type/badges/gpa.svg)](https://codeclimate.com/github/chippyash/Strong-Type)

The above badges represent the current development branch.  As a rule, I don't push
 to GitHub unless tests, coverage and usability are acceptable.  This may not be
 true for short periods of time; on holiday, need code for some other downstream
 project etc.  If you need stable code, use a tagged version. Read 'Further Documentation'
 and 'Installation'.
 
Please note that developer support for PHP5.3 was withdrawn at version 4.0.0 of this library.
It may be that the code will continue to run for you at later versions, but you must
ascertain that for yourself. If you need support for PHP 5.3, please use a version
`>=3,<4`

Also note that developer support for PHP5.4 & 5.5 was withdrawn at version 5.0.0 of this library.
It may be that the code will continue to run for you at later versions, but you must
ascertain that for yourself. If you need support for PHP 5.4 or 5.5, please use a version
`>=5,<6`
 
GMP support is tested on the Travis-ci build servers for PHP V5.6 as that is the only
version that stable gmp support is available for.   

See the [Test Contract](https://github.com/chippyash/Strong-Type/blob/master/docs/Test-Contract.md) in the docs directory.

## What?

Provides strong type implementations of base PHP types.  Adds some 'missing'
numeric types.

### Types supported

*  BoolType
*  DigitType
*  FloatType
*  ComplexType
*  IntType
*  NaturalIntType
*  WholeIntType
*  RationalType
*  StringType

The library is released under the [GNU GPL V3 or later license](http://www.gnu.org/copyleft/gpl.html)

## Why?

One of the joys of PHP is its loose typing, but there are situations, particularly
in large or complex systems where you want to guarantee that method parameters are
what you want them to be. PHP's type hinting extends to a few basic native types
such as arrays and hard typing to class names.  For the rest you end up having to
put a lot of boiler plate in your methods just to ensure that when you expect a
float for instance, you get one (or use hhvm ;-) ).  This library addresses the 
issue for some basic PHP types plus some extensions for what could be considered 
'missing' types.

The most common use case for this library is as a strong type hinter for your public methods:

<pre>
    
    public function myFunc(StringType $str, IntType $ival);
    
</pre>

Bingo: your function expects a StringType and and an IntType and nothing else!

The primary purpose of strong typing in this context is to *guard your public
methods against unwarranted side effects*.  Unwrap the native type at the point
of use.

The secondary use case is for when you want to start using some missing types fromPHP,
in particular Rational (fractions) and Complex numeric types.  I've built a reasonably 
comprehensive Mathematical Matrix library based on the numeric types from this library.

### PHP 7 warning

PHP 7 introduces type hinting for [native types](https://blog.engineyard.com/2015/what-to-expect-php-7)
but the problem is that you are going to get an automatic conversion or caste.  Not what I'd
expect. In the meantime, use this StongType library to harden your application.

That said, once you get into the swing of using the basic types, you'll find them most amenable to being passed
around and used interchangeably with PHP native types, primarily because they support a \__toString() method and 
\__invoke() which proxies to the get() method.

<pre>
    
    public function myFunc(StringType $str, IntType $ival)
    {
        echo $str;
        $foo = "The amount is {$ival}";
        $n = 2 * $ival();
    }
    
</pre>

## When

The current library covers basic data types plus some extensions.

If you want more, either suggest it, or better still, fork it and provide a pull request.

See [The Matrix Packages](http://the-matrix.github.io/packages/) for other packages from Chippyash

## How

### Coding Basics

#### Very simple way

This is how we use it in everyday work to ensure that our calls to some function
is conformant:

<pre>
function foo(FloatType $foo) {...}
$myFoo = foo(new FloatType(1);
</pre>

NB. The integer `1` is going to get converted into a float `1.0` but that is ok 
 because our method `foo()` expects a FloatType and with PHPs current type hinting
 can enforce it.

#### For those that have the stamina

Create a type via the Type Factory:

<pre>
    use Chippyash\Type\TypeFactory;
    $str = TypeFactory::create('string','foo');
    //or
    $str = TypeFactory::createString('foo');

    $int = TypeFactory::createInt(2);
    //or
    $int = Typefactory::create('int', 2);

    //some types can take two parameters
    $rat = TypeFactory::create('rational', 2, 3);
</pre>

etc, etc

Supported type tags are:

*  bool (or boolean)
*  digit
*  float (or double)
*  complex
*  int (or integer)
*  natural
*  whole
*  rational
*  string

Create one directly:

<pre>
    use Chippyash\Type\Number\Complex\ComplexType;
    use Chippyash\Type\Number\Rational\Rationaltype;
    use Chippyash\Type\String\DigitType;
    use Chippyash\Type\String\StringType;
    use Chippyash\Type\Number\FloatType;
    use Chippyash\Type\Number\IntType;
    $c = new ComplexType(new RationalType(new IntType(-2), new IntType(1)), new RationalType(new IntType(3), new IntType(4));
    $d = new DigitType(34);
    $s = new StringType('foo');
    $d2 = new DigitType('34foo'); // == '34'
    $r = new RationalType(new IntType(1), new IntType(2));
</pre>

etc, etc

Create a complex type via the Complex Type Factory (n.b. the Type Factory uses
this)

<pre>
    use Chippyash\Type\Number\Complex\ComplexTypeFactory;
    $c = ComplexTypeFactory::create('13-2.67i');
    //same as
    $c = ComplexTypeFactory::fromString('13-2.67i');

    $c = ComplexTypeFactory::create(2.4, new IntType(-6));
    $c = ComplexTypeFactory::create(2, -61.78);
    $c = ComplexTypeFactory::create(new FloatType(2), -61.78);
    //i.e. any pair of numeric, intType, FloatType or RationalType values can be used to create
    //a complex type via the factory
</pre>

Create a rational type via the Rational Type Factory (n.b. the Type Factory uses
this, but using it directly may give you finer grain control in some circumstances.)

<pre>
    use Chippyash\Type\Number\Rational\RationalTypeFactory;
    $r = RationalTypeFactory::create(M_1_PI);    //results in 25510582/80143857
    $r = RationalTypeFactory::fromFloat(M_1_PI); //ditto
    $r = RationalTypeFactory::fromFloat(M_1_PI, 1e-5); //results in 113/355
    $r = RationalTypeFactory::fromFloat(M_1_PI, 1e-17);  //results in 78256779/245850922

    $r = RationalTypeFactory::create('2/3');
    //same as
    $r = RationalTypeFactory::fromString('2/3');
</pre>

RationalTypeFactory::fromFloat obeys the current setting of RationalTypeFactory::$defaultTolerance.  The default
value is 1e-15.  You can change this by calling RationalTypeFactory::setDefaultFromFloatTolerance() at the beginning 
of your program.

All types support the TypeInterface:

*  get() - return the value as a PHP native type (if possible)
*  set($value) - set the value
*  \__toString() - Magic toString method. Return value as a string
*  \__invoke() - Proxy to get(), allows you to write $c() instead of $c->get()

Numeric types, that is IntType, WholeIntType, NaturalIntType, FloatType, RationalType
and ComplexType support the NumericTypeInterface which defines the methods

*  negate(): negate the number - NB Negation is will throw a \BadMethodCallException for WholeInt and NaturalInt types as they cannot be negative
*  asComplex(): returns a complex real representation of the number (e.g. 2+0i).  For
complex types, simply clones the existing object.
*  asRational(): returns rational representation of the number.  For rational types, simply clones the existing object.
*  asIntType(): returns number caste as IntType.  For IntType, simply clones the existing objeoct.
*  asFloatType(): returns number caste as FloatType.  For FloatType, simply clones the existing objeoct.
*  abs(): return the absolute value of the number
*  sign(): return sign of number: -1 == negative, 0 == zero, 1 == positive

IntTypes support two additional methods:

*  factors(): array: returns a sorted array of factors of the number
*  primeFactors(): array: returns \[primeFactor => exponent,...\] i.e the primeFactor => number of times it occurs

Additionally, the RationalType supports the RationalTypeInterface:

*  numerator() - return the integer value numerator
*  denominator() - return the integer value denominator

NB, AbstractRationalType amends the set() method to proxy to setFromTypes(), i.e. calling RationalType::set()
requires the same parameters as setFromTypes()

Additionally the ComplexType supports the ComplexTypeInterface:

*  r() - return the real part as a RationalType
*  i() - return the imaginary part as a RationalType
*  isZero() - Is this number equal to zero?
*  isReal() - Is this number a real number?  i.e. is it in form n+0i
*  isGuassian() - Is this number Gaussian, i.e r & i are both equivalent to integers
*  conjugate() - Return conjugate of this number
*  modulus() - Return the modulus, also known as absolute value or magnitude of this number
*  theta() - Return the angle (sometimes known as the argument) of the number when expressed in polar notation
*  radius() - Return the radius (sometimes known as Rho) of the number when expressed in polar notation
*  asPolar() - Returns complex number expressed in polar form i.e. an array \[radius, theta\]
*  polarQuadrant() - Returns the polar quadrant for the complex number
*  polarString() - Return complex number expressed as a string in polar form i.e. r(cosθ + i⋅sinθ)

NB, ComplexType amends the set() method to proxy to SetFromTypes, i.e. calling ComplexType::set()
requires the same parameters as setFromTypes().

There is no PHP native equivalent for a ComplexType, therefore the get() method proxies to the
\__toString() method and returns something in the form [-]a(+|-)bi e.g. '-2+3.6i' except where
The ComplexType->isReal() in which case get() will return a float or an int.  If you need to disambiguate
then use ComplexType::toFloat() which will throw an exception if the number is not real.

Polar form complex numbers are supported by the polar methods in ComplexType and also by the
ComplexTypeFactory::fromPolar(RationalType $radius, RationalType $theta) method.

### Support for GMP extension - V2 onwards only

The library automatically recognises the availability of the gmp extension and
will use it for int, rational and complex types.  

There is no gmp support for WholeIntType, NaturalIntType or FloatType.

- WholeIntType and NaturalIntType creation via the TypeFactory will return GMPIntType.
    - The validation for whole and natural numbers is ignored - they are treated as integers
- FloatType creation via the TypeFactory will return a GMPRationalType.

You can force the library to use PHP native types by calling

<pre>
    use Chippyash\Type\RequiredType; 
    RequiredType::getInstance()->set(RequiredType::TYPE_NATIVE);
</pre>

at the start of your code. This will in turn call the setNumberType methods on the
other factories, so you don't need to do that

If you want to get the gmp typed value of a number you can call its gmp() method.

<pre>
    //assuming we are running under gmp
    $i = TypeFactory::create('int', 2); //returns GMPIntType
    $i = TypeFactory::create('whole', -1); //returns GMPIntType
    $i = TypeFactory::create('natural', 0); //returns GMPIntType
    $gmp = $i->gmp(); //returns resource or GMP object depending on PHP version

    $r = TypeFactory::create('rational', 2, 3); //returns GMPRationalType
    $r = TypeFactory::create('float', 2/3); //returns GMPRationalType
    $gmp = $i->gmp(); //returns resource or GMP object depending on PHP version

    $r = TypeFactory::create('rational', 2, 3); //returns GMPRationalType
    $gmp = $r->gmp(); //returns array of gmp types, [numerator, denominator]

    $c = TypeFactory::create('complex', '2+3i'); //returns GMPComplexType
    $gmp = $c->gmp(); //returns array of gmp types, [[num,den],[num,den]] i.e. [r,i]
</pre>

All GMP types support the GMPInterface, so in addition to having the gmp() method,
they will also have:

*  public function asGMPIntType() Return number as GMPIntType number. Will return floor(n/d) for rational types    
*  public function asGMPComplex() : Return the number as a GMPComplex number i.e. n+0i
*  public function asGMPRational(): Return number as GMPRational number.

Trying to keep track of what types you are actually instantiating is made much easier if
you use the type factories, as they know which types to create.  **Therefore if
you want your code to be runnable as PHP native or GMP, use the factories to
create your numeric types.**

## Further documentation

You can find the [API documentation here](http://chippyash.github.io/Strong-Type)

[Test Contract](https://github.com/chippyash/Strong-Type/blob/master/docs/Test-Contract.md) in the docs directory.

Check out [ZF4 Packages](http://zf4.biz/packages?utm_source=github&utm_medium=web&utm_campaign=blinks&utm_content=strongtype) for more packages

### UML

![class diagram](https://github.com/chippyash/Strong-Type/blob/master/docs/strong-type-class.png)

## Changing the library

1.  fork it
2.  write the test
3.  amend it
4.  do a pull request

Found a bug you can't figure out?

1.  fork it
2.  write the test
3.  do a pull request

NB. Make sure you rebase to HEAD before your pull request

Or - raise an issue ticket.

## Where?

The library is hosted at [Github](https://github.com/chippyash/Strong-type). It is
available at [Packagist.org](https://packagist.org/packages/chippyash/strong-type)

### Installation

Install [Composer](https://getcomposer.org/)

#### For production

Use V5 unless you have a strong reason not to.
<pre>
    "chippyash/strong-type": ">=5.0.0,<6"
</pre>

V5 branch is the default, no further development of ealier versions will take place.
 
#### For development

Clone this repo, and then run Composer in local repo root to pull in dependencies

<pre>
    git clone git@github.com:chippyash/Strong-Type.git StrongType
    cd StrongType
    composer update
</pre>

To run the tests:

<pre>
    cd StrongType
    vendor/bin/phpunit -c test/phpunit.xml test/
</pre>

## License

This software library is released under the [GNU GPL V3 or later license](http://www.gnu.org/copyleft/gpl.html)

This software library is Copyright (c) 2014-2018, Ashley Kitson, UK

This software library contains code items that are: 

- Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
- released under the New BSD License

In particular the code items are:

- elements of Chippyash\Type\String\DigitType
- all of Chippyash\Zend\ErrorHandler

None of the contained code items breaks the overriding license, or vice versa,  as far as I can tell. 
So as long as you stick to GPL V3+ then you are safe. If at all unsure, please seek appropriate advice.

If the original copyright owners of the included code items object to this inclusion, please contact the author.

A commercial license is available for this software library, please contact the author. 
It is normally free to deserving causes, but gets you around the limitation of the GPL
license, which does not allow unrestricted inclusion of this code in commercial works.

This library is supported by <a href="https://www.jetbrains.com"><img src="https://github.com/chippyash/Strong-Type/raw/master/img/JetBrains.png" alt="Jetbrains" style="height: 200px;vertical-align: middle;"></a>
who provide their IDEs to Open Source developers.


## History

V0...  pre releases

V1.0.0 Original release

V1.0.1 Remove requirement for zendfilter package to reduce dependency footprint

       Add NumericTypeInterface to support other usages of library

V1.0.2 Add conjugate method to complex type

       rebase wholeInt and naturalInt type on intType

V1.0.3 Add modulus method to complex type

V1.0.4 Fix RationalTypefactory::fromFloat not recognising zero

V1.0.5 Add negate() method to numeric types

V1.0.6 Add isReal() method for complex numbers

        add toFloat() method for complex numbers if number isReal()

V1.0.7 Add toComplex() method for numeric types

V1.0.8 Add abs() method for numeric types

V1.0.9 Add asRational, asFloatType and asIntType methods for numeric types. Rename toComplex -> asComplex method

V1.0.10 Refactor Typefactory to use as... methods

V1.0.11 Ensure isolation of type parts in as... methods

V1.1.0 Add Polar form complex number support
        
        move interfaces to separate folder

V1.1.1 Remove hard coded tolerance levels to fromFloat. Use default 1e-15 instead.

V1.1.2 Ensure clone clones inner objects correctly

V1.1.3 Refactor in preparation for supporting GMP types

V2.0.0 Add GMP support

V2.0.1 Additional gmp type checking

V2.0.2 update Zend dependencies

V2.0.3 fix tests breaking if GMP not installed - will skip properly

V2.0.4 add homepage to composer.json definition

V2.0.5 small amend to fix complex creation problem for calculator

V2.0.6 update phpunit to ~V4.3.0

V2.0.7 add test contract

V2.0.8 when GMP support enabled:

- WholeIntType and NaturalIntType creation via the TypeFactory will return GMPIntType.
- FloatType creation via the TypeFactory will return a GMPRationalType.
- RationalType creation via the TypeFactory will return a GMPRationalType.
- ComplexType creation via the TypeFactory will return a GMPComplexType.

V2.0.9 fix merge

V2.1.0 downgrade library to support PHP5.3 - too many people still using it!

V2.1.1 fix PHP5.3 unit tests

V2.1.2 remove dependency on Zend\StdLib

V2.1.3 refactor for code cleanliness

V2.1.4 deprecate Typefactory::setNumberType() method

V3.0.0 BC Break: Rename namespace from chippyash\Type to Chippyash\Type

V3.0.1 add link to packages

V3.0.2 verify PHP7 compatibility

V4.0.0 BC Break: end PHP5.3 support. build script changes

V4.0.1 update composer - forced by packagist composer.json format change

V5.0.0 BC Break: end of support for PHP <5.6



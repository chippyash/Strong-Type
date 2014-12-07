# chippyash/Type

## Request for help

The V2 build of this library can use GMP support. I can't seem to figure out how to add gmp support 
to the travis ci build system.  If you know how to do this, I'd appreciate your
intervention and assistance.  The following QA is still good, as the gmp tests 
specify a requirement for gmp and will therefore be passed over. 

## Quality Assurance

[![Build Status](https://travis-ci.org/chippyash/Strong-Type.svg?branch=master)](https://travis-ci.org/chippyash/Strong-Type)
[![Coverage Status](https://coveralls.io/repos/chippyash/Strong-Type/badge.png)](https://coveralls.io/r/chippyash/Strong-Type)

See above request for help: gmp is not being tested at the present time by the
Travis CI servers.  The gmp specific tests do run locally - I promise!  See the
(Test Contract)[https://github.com/chippyash/Strong-Type/blob/master/docs/Test-Contract.md] in the docs directory.

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
what you want them to be.  PHPs type hinting extends to a few basic native types
such as arrays and hard typing to class names.  For the rest you end up having to
put a lot of boiler plate in your methods just to ensure that when you expect a
float for instance, you get one (or use hhvm ;-) ).  This library addresses the 
issue for some basic PHP types plus some extensions for what could be considered 
'missing' types.

The primary purpose of strong typing in this context is to guard your public
methods against unwarranted side effects.  You should not consider it necessary
in most circumstances to have to pass around these types internally, except of
course where it makes sense to do so, e.g. unwrap the native type at the point
of use.

A secondary purpose is to support the chippyash/Math-Matrix and chippyash/Math-Type-Calculator
libraries. This is why you will see some small addendum being made to this library
on a continual basis.  

## When

The current library covers basic data types plus some extensions.

If you want more, either suggest it, or better still, fork it and provide a pull request.

Check out [chippyash/Math-Type-Calculator](https://github.com/chippyash/Math-Type-Calculator) for a library that
provides arithmetic support for the numeric strong types in this library.

Check out [chippyash/Matrix](https://github.com/chippyash/Matrix) for Matrix data type support.

Check out [chippyash/Logical-Matrix](https://github.com/chippyash/Logical-matrix) for logical matrix operations

Check out [chippyash/Math-Matrix](https://github.com/chippyash/Math-Matrix) for mathematical matrix operations

Check out [chippyash/Builder-Pattern](https://github.com/chippyash/Builder-Pattern) for an implementation of the Builder Pattern for PHP

Check out [chippyash/Testdox-Converter](https://github.com/chippyash/Testdox-Converter) for a utility to create markdown format test contract from phpunit testdox-html

## How

You can find the (API documentation here)[http://chippyash.github.io/Strong-Type]

### Coding Basics

Create a type via the Type Factory:

<pre>
    use chippyash\Type\TypeFactory;
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
    use chippyash\Type\Number\Complex\ComplexType;
    use chippyash\Type\Number\Rational\Rationaltype;
    use chippyash\Type\String\DigitType;
    use chippyash\Type\String\StringType;
    use chippyash\Type\Number\FloatType;
    use chippyash\Type\Number\IntType;
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
    use chippyash\Type\Number\Complex\ComplexTypeFactory;
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
    use chippyash\Type\Number\Rational\RationalTypeFactory;
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
will use it for int, rational and complex types.  There is no gmp support for 
wholeIntType, NaturalIntType or FloatType.  You can force the library to use
PHP native types by calling

<pre>
    TypeFactory::setNumberType(TypeFactory::TYPE_NATIVE);
</pre>

at the start of your code. This will in turn call the setNumberType methods on the
other factories, so you don't need to do that

If you want to get the gmp typed value of a number you can call its gmp() method.

<pre>
    //assuming we are running under gmp
    $i = TypeFactory::create('int', 2); //returns GMPIntType
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
you use the type factories, as they know which types to create.  Therefore if
you want your code to be runnable as PHP native or GMP, use the factories to
create your numeric types.

### Changing the library

1.  fork it
2.  write the test
3.  amend it
4.  do a pull request

Found a bug you can't figure out?

1.  fork it
2.  write the test
3.  do a pull request

NB. Make sure you rebase to HEAD before your pull request

## Where?

The library is hosted at [Github](https://github.com/chippyash/Strong-type). It is
available at [Packagist.org](https://packagist.org/packages/chippyash/strong-type)

### Installation

Install [Composer](https://getcomposer.org/)

#### For production

If you do not need GMP support, you can continue to use the V1.1 branch for the
time being.
 
add

<pre>
    "chippyash/strong-type": "~1.1.3"
</pre>

to your composer.json "requires" section

If you want GMP support, use the V2 branch

<pre>
    "chippyash/strong-type": "~2.0.0"
</pre>

At some point in the not too distant future, the V2 branch will become the default,
once downstream development of the type calculator to support GMP is complete.
 
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


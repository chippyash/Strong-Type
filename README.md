# chippyash/Type

## Quality Assurance

[![Build Status](https://travis-ci.org/chippyash/Strong-Type.svg?branch=master)](https://travis-ci.org/chippyash/Strong-Type)
[![Coverage Status](https://coveralls.io/repos/chippyash/Strong-Type/badge.png)](https://coveralls.io/r/chippyash/Strong-Type)


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
in large or complex systems where you want guarantee that method parameters are
what you want them to be.  PHPs type hinting extends to a few basic native types
such as arrays and hard typing to class names.  For the rest you end up having to
put a lot of boiler plate in your methods just to ensure that when you expect a
float for instance, you get one.  This library addresses the issue for some basic
PHP types plus some extensions for what could be considered 'missing' types.

The primary purpose of strong typing in this context is to guard your public
methods against unwarranted side effects.  You should not consider it necessary
in most circumstances to have to pass around these types internally, except of
course where it makes sense to do so, e.g. unwrap the native type at the point
of use.

A secondary purpose is to support the chippyash/Math-Matrix and chippyash/Math-Type-Calculator
libraries. This is why you will see some small addendum being made to this library
on a continual basis.  In due course I'll add support for the numeric types recognising
gmp and bcmath extension availability automatically.

## When

The current library covers basic data types plus some extensions.

If you want more, either suggest it, or better still, fork it and provide a pull request.

## How

### Coding Basics

Create a type via the Type Factory:

<pre>
    use chippyash\Type\TypeFactory;
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
    use chippyash\Type\Number\FloatType;
    use chippyash\Type\Number\IntType;
    $c = new ComplexType(new FloatType(-2), new FloatType(3));
    $d = new DigitType(34);
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
    //i.e. any pair of numeric, intType or FloatType values can be used to create
    //a complex type via the factory
</pre>

Create a rational type via the Rational Type Factory (n.b. the Type Factory uses
this, but using it directly may give you finer grain control in some circumstances.)

<pre>
    use chippyash\Type\Number\Rational\RationalTypeFactory;
    $r = RationalTypeFactory::create(M_1_PI);    //results in 113/355
    $r = RationalTypeFactory::fromFloat(M_1_PI); //ditto
    $r = RationalTypeFactory::fromFloat(M_1_PI, 1e-17);  //results in 78256779/245850922

    $r = RationalTypeFactory::create('2/3');
    //same as
    $r = RationalTypeFactory::fromString('2/3');
</pre>

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

*  setFromTypes(IntType $num, IntType $den, BoolType $reduce = null) - strict typed setter method
*  numerator() - return the integer value numerator
*  denominator() - return the integer value denominator

NB, AbstractRationalType amends the set() method to proxy to setFromTypes(), i.e. calling RationalType::set()
requires the same parameters as setFromTypes()

Additionally the ComplexType supports the ComplexTypeInterface:

*  setFromTypes(FloatType $real, FloatType $imaginary) - strict typed setter method
*  r() - return the real part as a float
*  i() - return the imaginary part as a float

NB, ComplexType amends the set() method to proxy to SetFromTypes, i.e. calling ComplexType::set()
requires the same parameters as setFromTypes().

There is no PHP native equivalent for a ComplexType, therefore the get() method proxies to the
\__toString() method and returns something in the form [-]a(+|-)bi e.g. '-2+3.6i' except where
The ComplexType->isReal() in which case get will return a float.  If you need to disambiguate
then use ComplexType::toFloat() which will throw an exception if the number is not real.

Complex numbers support some additional attributes:

*  isZero(): r() == i() == 0
*  isGaussian(): is_int(r()) && is_int(i())
*  isReal(): i() == 0 (real numbers expressed as complex type in form n+0i)

and three methods

*  conjugate(): returns conjugate of the complex number
*  modulus(): returns the modulus, also known as absolute value or magnitude of the complex number.
For complex numbers abs() === modulus()
*  toFloat(): returns PHP float equivalent if isReal() else throws chippyash\Type\Exceptions\NotRealComplexException

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

add

<pre>
    "chippyash/strong-type": ">=1.0.11"
</pre>

to your composer.json "requires" section

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
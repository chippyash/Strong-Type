# chippyash/Type

## Quality Assurance


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

A secondary purpose is to act as a simple filter.  For instance the DigitType
simply strips the construction value of any non digit elements.

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
    use chippyash\Type\Number\ComplexType;
    use chippyash\Type\String\DigitType;
    use chippyash\Type\Number\FloatType;
    $c = new ComplexType(new FloatType(-2), new FloatType(3));
    $d = new DigitType(34);
    $d2 = new DigitType('34foo'); // == '34'
</pre>

Create a complex type via the Complex Type Factory (n.b. the Type Factory uses
this, but using it directly may give you finer grain control in some circumstances.)

<pre>
    use chippyash\Type\Number\ComplexTypeFactory;
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
    use chippyash\Type\Number\RationalTypeFactory;
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

Additionally, the RationalType supports the RationalTypeInterface:

*  setFromTypes(IntType $num, IntType $den, BoolType $reduce = null) - strict typed setter method
*  numerator() - return the integer value numerator
*  denominator() - return the integer value denominator

NB, AbstractRationalType amends the set() method to proxy to setFromTypes(), i.e. calling RationalType::set()
requires the same parameters as setFromType()

Additionally the ComplexType supports the ComplexTypeInterface:

*  setFromTypes(FloatType $real, FloatType $imaginary) - strict typed setter method
*  r() - return the real part as a float
*  i() - return the imaginary part as a float

NB, ComplexType amends the set() method to proxy to SetFromTypes, i.e. calling ComplexType::set()
requires the same parameters as setFromType().

There is no PHP native equivalent for a ComplexType, therefore the get() method proxies to the
\__toString() method and returns something in the form [-]a(+|-)bi e.g. '-2+3.6i'

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
    "chippyash/strong-type": "~1.0.0"
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

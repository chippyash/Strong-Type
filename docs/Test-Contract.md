# Chippyash Strong Types

## chippyash\Test\Type\AbstractMultiValueType

*  Magic to string returns empty string
*  Get invokes get as native type
*  Call to set from types throws exception
*  Classes and native types can be used to define value map
*  Set from types catches invali object types
*  Set from types catches invali native types

## chippyash\Test\Type\AbstractType

*  Constructor will return abstract type
*  Set followed by get returns a value
*  Magic to string returns a string
*  Magic invoke returns value
*  Clone does clone inner value

## chippyash\Test\Type\BoolType

*  Construct without value throws exception
*  Get returns boolean
*  Get returns only true or false
*  Magic to string returns string

## chippyash\Test\Type\Number\Complex\AbstractComplexType

*  Radius and abs proxy to modulus
*  Theta will return value
*  Polar string will return value

## chippyash\Test\Type\Number\Complex\ComplexTypeFactory

*  Create with invalid string as first parameter throws exception
*  Create with valid string containing float as first parameter returns complex type
*  Create with valid string containing rational as first parameter returns complex type
*  Create with non string as first param and null as second param throws exception
*  Create with unsupported param types throws exception
*  Create with correct param types returns complex type
*  Create from polar returns complex type

## chippyash\Test\Type\Number\Complex\ComplexType

*  Construct expects first parameter to be float type
*  Construct expects second parameter to be float type
*  Construct with two rational type parameters returns complex type
*  Set expects first parameter to be rational type
*  Set expects second parameter to be rational type
*  Set with two rational type parameters will return complex type
*  Set with less than two parameter throws exception
*  Set with more than two parameter throws exception
*  Set with two parameter returns complex type
*  R returns rational
*  I returns rational
*  Is zero returns true if complex is zero
*  Is zero returns false if complex is not zero
*  Is gaussian for both parts being integer values returns true
*  Is gaussian for one part not being integer values returns false
*  Conjugate returns correct complex type
*  Modulus for zero complex number is zero
*  Modulus for real returns abs real
*  Triangle inequality for modulus
*  Commutative multiplication attribute for modulus
*  Modulus returns correct result
*  Can negate the number
*  Is real returns true for real number
*  Is real returns false for not real number
*  Get returns zero integer for zero complex number
*  Get returns integer for integer real complex number
*  Get returns float for float real complex number
*  Get returns string for non real complex number
*  Magic to string returns string
*  Get returns string for complex number
*  Magic invoke returns string for complex number
*  Magic invoke returns int for real integer complex number
*  Magic invoke returns float for real float complex number
*  To float throws exception for non real complex number
*  To float returns float for real float complex number
*  To float returns integer for integer float complex number
*  As complex returns clone of self
*  As rational returns rational type
*  As float type returns float type
*  As int type returns int type
*  As rational for non real throws exception
*  As float type for non real throws exception
*  As int type for non real throws exception
*  Abs returns absolute value
*  Radius returns correct value
*  Theta returns correct value
*  As polar returns correct values
*  Polar quadrant returns correct quadrant
*  Polar string for zero complex returns zero string
*  Polar string for non zero complex returns non zero string
*  Clone does clone inner value

## chippyash\Test\Type\Number\Complex\GMPComplexTypeFactory

*  Create with invalid string as first parameter throws exception
*  Create with valid string containing float as first parameter returns complex type
*  Create with valid string containing rational as first parameter returns complex type
*  Create with non string as first param and null as second param throws exception
*  Create with unsupported param types throws exception
*  Create with correct param types returns complex type
*  Create from polar returns complex type
*  Set bad number type throws eception
*  Creation will use gmp automatically if it exists

## chippyash\Test\Type\Number\Complex\GMPComplexType

*  Construct expects first parameter to be float type
*  Construct expects second parameter to be rational type
*  Construct with two g m p rational type parameters returns g m p complex type
*  Set expects first parameter to be g m p rational type
*  Set expects second parameter to be g m p rational type
*  Set with two g m p rational type parameters will return g m p complex type
*  Set with less than two parameter throws exception
*  Set with more than two parameter throws exception
*  R returns rational
*  I returns rational
*  Is zero returns true if complex is zero
*  Is zero returns false if complex is not zero
*  Is gaussian for both parts being integer values returns true
*  Conjugate returns correct g m p complex type
*  Modulus for zero complex number is zero
*  Triangle inequality for modulus
*  Commutative multiplication attribute for modulus
*  Modulus returns correct result
*  Can negate the number
*  Is real returns true for real number
*  Is real returns false for not real number
*  Get returns integer for integer real number
*  Get returns float for float real number
*  Magic to string returns string
*  Get returns string for complex number
*  Magic invoke returns string for complex number
*  Magic invoke returns int for real integer complex number
*  Magic invoke returns float for real float complex number
*  To float throws exception for non real complex number
*  To float returns float for real float complex number
*  To float returns integer for integer float complex number
*  As complex returns native complex type
*  As rational returns rational type
*  As float type returns float type
*  As int type returns int type
*  As rational for non real throws exception
*  As float type for non real throws exception
*  As int type for non real throws exception
*  Abs returns absolute value
*  Theta returns g m p rational type
*  Polar string for zero complex returns zero string
*  Polar string for non zero complex returns non zero string
*  Gmp returns array
*  As gmp int type throws exception for non real complex
*  As gmp complex returns clone
*  As gmp rational returns gmp rational for real complex
*  As gmp rational throws exception for non real complex

## chippyash\Test\Type\Number\FloatType

*  Float type converts values to float
*  Can negate the number
*  As complex returns complex type
*  As rational returns rational type
*  As float type returns float type
*  As int type returns int type
*  Abs returns absolute value

## chippyash\Test\Type\Number\GMPIntType

*  G m p int type converts values to integer
*  Can negate the number
*  As complex returns complex type
*  As rational returns rational type
*  As float type returns float type
*  As int type returns g m p int type
*  Abs returns absolute value
*  Factors returns an array of factors of the number
*  Prime factors returns an array of factors of the number
*  As gmp int type clones original
*  As gmp complex returns gmp complex type
*  As gmp rational returns gmp rational type

## chippyash\Test\Type\Number\IntType

*  Int type converts values to integer
*  Int type can be used in calculation
*  Can negate the number
*  As complex returns complex type
*  As rational returns rational type
*  As float type returns float type
*  As int type returns int type
*  Abs returns absolute value
*  Factors returns an array of factors of the number
*  Prime factors returns an array of factors of the number

## chippyash\Test\Type\Number\NaturalIntType

*  Natural int type converts to integer
*  Construct natural int with integer less than one throws exception
*  Cannot negate the number

## chippyash\Test\Type\Number\Rational\AbstractRationalType

*  Magic invoke proxies to get
*  Set returns object
*  Set expects at least two parameters
*  Set proxies to set from types with two parameters expects int type parameters
*  Numerator returns value
*  Denominator returns value
*  Get returns value
*  As complex returns complex type
*  As rational returns rational type
*  As float type returns float type
*  As int type returns int type

## chippyash\Test\Type\Number\Rational\GMPRationalTypeFactory

*  Create from valid string value returns ratioanal type
*  Create from numeric value with no denominator specified returns ratioanal type
*  Create from numeric value with denominator specified returns ratioanal type
*  Create from int types returns rational type
*  Create from int type numerator and no denominator returns rational type
*  Create from int type numerator and numeric denominator returns rational type
*  Create from numeric numerator and int type denominator returns rational type
*  Create from float type returns rational type
*  Create from invalid string throws exception
*  Create from unsupported type for numerator throws exception
*  Create from unsupported type for denominator throws exception
*  From float uses default tolerance if not given
*  From float uses accepts php float tolerance value
*  From float uses accepts float type tolerance value
*  From float with zero value returns zero as string
*  Set default from float tolerance is static
*  Set number type to default will set gmp if available

## chippyash\Test\Type\Number\Rational\GMPRationalType

*  Construct expects first parameter to be g m p int type
*  Construct expects second parameter to be g m p int type
*  Construct expects third parameter to be bool type if given
*  Construct with third parameter set false will not reduce
*  Numerator returns integer
*  Denominator returns integer
*  Negative denominator normalizes to negative numerator
*  Get returns gmp type
*  Magic to string returns string value
*  Get returns int for whole fraction
*  Can negate the number
*  Abs returns absolute value
*  Magic invoke proxies to get
*  Set returns value
*  Set expects at least two parameters
*  Set proxies to set from types with two parameters expects g m p int type parameters
*  Set proxies to set from types with three parameters expects bool type third parameter
*  Set proxies to set from types with two correct parameters
*  As complex returns complex type
*  As rational returns rational type
*  As float type returns float type
*  As int type returns int type
*  Gmp returns array of gmp types
*  As gmp int type returns gmp int type
*  As gmp rational returns clone of self
*  As gmp complex returns gmp complex

## chippyash\Test\Type\Number\Rational\RationalTypeFactory

*  Create from valid string value returns ratioanal type
*  Create from numeric value with no denominator specified returns ratioanal type
*  Create from numeric value with denominator specified returns ratioanal type
*  Create from int types returns rational type
*  Create from int type numerator and no denominator returns rational type
*  Create from int type numerator and numeric denominator returns rational type
*  Create from numeric numerator and int type denominator returns rational type
*  Create from float type returns rational type
*  Create from invalid string throws exception
*  Create from unsupported type for numerator throws exception
*  Create from unsupported type for denominator throws exception
*  From float uses default tolerance if not given
*  From float uses accepts php float tolerance value
*  From float uses accepts float type tolerance value
*  From float with zero value returns zero as string
*  Set default from float tolerance is static

## chippyash\Test\Type\Number\Rational\RationalType

*  Construct expects first parameter to be int type
*  Construct expects second parameter to be int type
*  Construct expects third parameter to be bool type if given
*  Construct with third parameter set false will not reduce
*  Numerator returns integer
*  Denominator returns integer
*  Negative denominator normalizes to negative numerator
*  Get returns float
*  Can negate the number
*  Abs returns absolute value
*  Clone does clone inner value

## chippyash\Test\Type\Number\WholeIntType

*  Whole int type converts to integer
*  Construct whole int with integer less than zero throws exception
*  Cannot negate the number

## chippyash\Test\Type\String\DigitType

*  Digit type converts strings with numbers stripping non digits
*  Convert non stringable type throws exception

## chippyash\Test\Type\String\StringType

*  String type converts base types to string
*  String type proxies magic invoke to get
*  String type can be used in string concatenation

## chippyash\Test\Type\TypeFactory

*  Factory create method returns correct type
*  Create invalid type throws exception
*  Create int returns int type
*  Create float returns float type
*  Create string returns string type
*  Create bool returns bool type
*  Create digit returns digit type
*  Create whole returns whole int type
*  Create natural returns natural int type
*  Create rational returns rational type
*  Create complex returns complex type
*  Create int with non numeric throws exception
*  Create whole int with non numeric throws exception
*  Create natural int with non numeric throws exception
*  Create float with non numeric throws exception
*  Create with numeric type interface parameter returns numeric type interface
*  Set number type to default will set gmp if available
*  Set number type to invalid type throws exception
*  Creating whole ints via type factory under gmp will return g m p int type
*  Creating natural ints via type factory under gmp will return g m p int type
*  Creating floats via type factory under gmp will return g m p rational type


Generated by chippyash/testdox-converter
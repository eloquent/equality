# Equality

*A better strict comparison for PHP.*

[![The most recent stable version is 2.1.2][version-image]][Semantic versioning]
[![Current build status image][build-image]][Current build status]
[![Current coverage status image][coverage-image]][Current coverage status]

## Deprecated

*Equality* is deprecated. Please use [Parity] instead.

## Installation and documentation

- Available as [Composer] package [eloquent/equality].
- [API documentation] available.

## The problem

Sometimes it is necessary to compare two objects to determine whether they are
considered equal.

If the `==` operator is used, there is no strictness about the equality. For
instance, this snippet outputs 'equal':

```php
$left = new stdClass;
$left->foo = 0;

$right = new stdClass;
$right->foo = null;

if ($left == $right) {
    echo 'equal';
} else {
    echo 'not equal';
}
```

Conversely, if the `===` operator is used, objects are not equal unless they are
the same *instance*. The following snippet outputs 'not equal':

```php
$left = new stdClass;
$left->foo = 'bar';

$right = new stdClass;
$right->foo = 'bar';

if ($left === $right) {
    echo 'equal';
} else {
    echo 'not equal';
}
```

Unfortunately PHP does not have an inbuilt method to compare objects strictly
without requiring that they be the same instance. This is where *Equality* comes
in. This snippet correctly outputs 'equal':

```php
use Eloquent\Equality\Comparator;

$left = new stdClass;
$left->foo = 'bar';

$right = new stdClass;
$right->foo = 'bar';

$comparator = new Comparator;

if ($comparator->equals($left, $right)) {
    echo 'equal';
} else {
    echo 'not equal';
}
```

## Usage

*Equality* is very simple to use. Simply instantiate a `Comparator` and use its
`equals()` method:

```php
use Eloquent\Equality\Comparator;

$comparator = new Comparator;

if ($comparator->equals($left, $right)) {
    // equal
} else {
    // not equal
}
```

*Equality* can work with any PHP data type, not just objects.

## Custom equality logic

In some cases it may be desirable to customize how equality is determined.
The interface `EqualityComparable` can be used to provide a custom equality
implementation for any class:

```php
use Eloquent\Equality\Comparator;
use Eloquent\Equality\EqualityComparable;

class Foo implements EqualityComparable
{
    /**
     * @param mixed $value
     * @param Comparator $comparator
     *
     * @return boolean
     */
    public function isEqualTo($value, Comparator $comparator)
    {
        // custom logic...
    }
}
```

When *Equality* encounters an object that implements `EqualityComparable`, it
will return the result of the `isEqualTo()` method instead of using the default
equality logic. The comparator itself will be passed as the second parameter.

## How does *Equality* work?

*Equality* uses [reflection] to recurse over the values it is passed and ensure
that they are deeply, and strictly, equal.

In addition, it implements special protections to avoid infinite recursion
issues, such as objects that contain themselves, or objects that contain the
object that they are being compared to.

<!-- References -->

[Parity]: https://github.com/IcecaveStudios/parity
[reflection]: http://php.net/reflection

[API documentation]: http://lqnt.co/equality/artifacts/documentation/api/
[Composer]: http://getcomposer.org/
[build-image]: http://img.shields.io/travis/eloquent/equality/develop.svg "Current build status for the develop branch"
[Current build status]: https://travis-ci.org/eloquent/equality
[coverage-image]: http://img.shields.io/coveralls/eloquent/equality/develop.svg "Current test coverage for the develop branch"
[Current coverage status]: https://coveralls.io/r/eloquent/equality
[eloquent/equality]: https://packagist.org/packages/eloquent/equality
[Semantic versioning]: http://semver.org/
[version-image]: http://img.shields.io/:semver-2.1.2-brightgreen.svg "This project uses semantic versioning"

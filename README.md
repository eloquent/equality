# Equality

*A better strict comparison for PHP.*

## Installation

Equality requires PHP 5.3 or later.

### With [Composer](http://getcomposer.org/)

* Add 'eloquent/equality' to the project's composer.json dependencies
* Run `composer install`

### Bare installation

* Clone from GitHub: `git clone git://github.com/eloquent/equality.git`
* Use a [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
  compatible autoloader (namespace 'Eloquent\Equality' in the 'src' directory)

## The problem

Sometimes it is necessary to compare two objects to determine whether they are
considered equal.

If you use `==`, you cannot be strict about equality. For instance, this snippet
outputs 'equal':

```php
<?php

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

Conversely, if you use `===`, objects are not equal unless they are the same
*instance*. The following snippet outputs 'not equal':

```php
<?php

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
without requiring that they be the same instance. This is where Equality comes
in. This snippet correctly outputs 'equal':

```php
<?php

$left = new stdClass;
$left->foo = 'bar';

$right = new stdClass;
$right->foo = 'bar';

$comparator = new Eloquent\Equality\Comparator;

if ($comparator->equals($left, $right)) {
    echo 'equal';
} else {
    echo 'not equal';
}
```

## Usage

Equality is very simple to use. Simply instantiate a Comparator and use its
`equals()` method:

```php
<?php

$comparator = new Eloquent\Equality\Comparator;

if ($comparator->equals($left, $right)) {
    // equal
} else {
    // not equal
}
```

Equality can work with any PHP data type, not just objects.

## How does Equality work?

Equality uses [reflection](http://php.net/reflection) to recurse over the values
it is passed and ensure that they are deeply, and strictly, equal.

In addition, it implements special protections to avoid infinite recursion
issues, such as an object that contains itself.

## Code quality

Equality strives to attain a high level of quality. A full test suite is
available, and code coverage is closely monitored.

### Latest revision test suite results
[![Build Status](https://secure.travis-ci.org/eloquent/equality.png)](http://travis-ci.org/eloquent/equality)

### Latest revision test suite coverage
<http://ci.ezzatron.com/report/equality/coverage/>

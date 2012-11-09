<?php

/*
 * This file is part of the Equality package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// @codeCoverageIgnoreStart

namespace Eloquent\Equality;

use ReflectionObject;

interface EqualityComparable
{
    /**
     * @param mixed $value
     * @param Comparator|null $comparator
     *
     * @return boolean
     */
    public function isEqualTo($value, Comparator $comparator = null);
}

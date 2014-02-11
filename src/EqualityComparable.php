<?php

/*
 * This file is part of the Equality package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Equality;

/**
 * The interface implemented by objects with custom equality comparison rules.
 */
interface EqualityComparable
{
    /**
     * Returns true if the supplied value is deeply equal to this object.
     *
     * @deprecated Use icecave/parity instead.
     * @link https://github.com/IcecaveStudios/parity
     *
     * @param mixed      $value      The value to compare against.
     * @param Comparator $comparator The comparator to use.
     *
     * @return boolean True if the supplied value is deeply equal.
     */
    public function isEqualTo($value, Comparator $comparator);
}

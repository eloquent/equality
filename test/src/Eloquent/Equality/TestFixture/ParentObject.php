<?php

/*
 * This file is part of the Equality package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Equality\TestFixture;

class ParentObject
{
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public static $staticProperty = 'staticPropertyValue';
    private $foo;
    private $bar;
}

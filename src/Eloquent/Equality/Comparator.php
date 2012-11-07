<?php

/*
 * This file is part of the Equality package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Equality;

use ReflectionObject;

class Comparator
{
    /**
     * @param mixed $left
     * @param mixed $right
     *
     * @return boolean
     */
    public function equals($left, $right)
    {
        switch (gettype($left)) {
            case 'array':
                return $this->arrayEquals($left, $right);
            case 'object':
                return $this->objectEquals($left, $right);
        }

        return $left === $right;
    }

    /**
     * @param array $left
     * @param mixed $right
     *
     * @return boolean
     */
    protected function arrayEquals(array $left, $right)
    {
        if (!is_array($right)) {
            return false;
        }
        if (array_keys($left) !== array_keys($right)) {
            return false;
        }

        foreach ($left as $key => $value) {
            if (!$this->equals($value, $right[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param object $left
     * @param mixed  $right
     *
     * @return boolean
     */
    protected function objectEquals($left, $right)
    {
        if (!is_object($right)) {
            return false;
        }
        if (get_class($left) !== get_class($right)) {
            return false;
        }

        return $this->arrayEquals(
            $this->objectProperties($left),
            $this->objectProperties($right)
        );
    }

    /**
     * @param object $object
     *
     * @return array<string,mixed>
     */
    protected function objectProperties($object)
    {
        $properties = array();
        $reflector = new ReflectionObject($object);

        while ($reflector) {
            foreach ($reflector->getProperties() as $property) {
                $key = sprintf(
                    '%s::%s',
                    $property->getDeclaringClass()->getName(),
                    $property->getName()
                );

                $property->setAccessible(true);
                $properties[$key] = $property->getValue($object);
            }

            $reflector = $reflector->getParentClass();
        }

        return $properties;
    }
}

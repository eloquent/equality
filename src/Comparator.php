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

use ReflectionObject;

/**
 * Deeply compares values.
 *
 * @deprecated Use icecave/parity instead.
 * @link https://github.com/IcecaveStudios/parity
 */
class Comparator
{
    /**
     * Compare two values.
     *
     * @deprecated Use icecave/parity instead.
     * @link https://github.com/IcecaveStudios/parity
     *
     * @param mixed $left  The left value.
     * @param mixed $right The right value.
     *
     * @return boolean True if the values are deeply equal.
     */
    public function equals($left, $right)
    {
        $this->objectComparisonStack = array();

        return $this->valueEquals($left, $right);
    }

    /**
     * Compare two values.
     *
     * @deprecated Use icecave/parity instead.
     * @link https://github.com/IcecaveStudios/parity
     *
     * @param mixed $left  The left value.
     * @param mixed $right The right value.
     *
     * @return boolean True if the values are deeply equal.
     */
    public function __invoke($left, $right)
    {
        return $this->equals($left, $right);
    }

    /**
     * Compare two values.
     *
     * @param mixed $left  The left value.
     * @param mixed $right The right value.
     *
     * @return boolean True if the values are deeply equal.
     */
    protected function valueEquals($left, $right)
    {
        if ($left instanceof EqualityComparable) {
            return $left->isEqualTo($right, $this);
        } elseif ($right instanceof EqualityComparable) {
            return $right->isEqualTo($left, $this);
        }

        switch (gettype($left)) {
            case 'array':
                return $this->arrayEquals($left, $right);
            case 'object':
                return $this->objectEquals($left, $right);
        }

        return $left === $right;
    }

    /**
     * Compare an array to another arbitrary value.
     *
     * @param array $left  The left value.
     * @param mixed $right The right value.
     *
     * @return boolean True if the values are deeply equal.
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
            if (!$this->valueEquals($value, $right[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compare an object to another arbitrary value.
     *
     * @param object $left  The left value.
     * @param mixed  $right The right value.
     *
     * @return boolean True if the values are deeply equal.
     */
    protected function objectEquals($left, $right)
    {
        if (!is_object($right)) {
            return false;
        }
        if (get_class($left) !== get_class($right)) {
            return false;
        }

        $stackKey = $this->objectComparisonStackKey($left, $right);
        if (array_key_exists($stackKey, $this->objectComparisonStack)) {
            return true;
        }
        $this->objectComparisonStack[$stackKey] = true;

        return $this->arrayEquals(
            $this->objectProperties($left),
            $this->objectProperties($right)
        );
    }

    /**
     * Get the properties of the supplied object, including protected and
     * private values.
     *
     * @param object $object The object to inspect.
     *
     * @return array<string,mixed> The object's properties.
     */
    protected function objectProperties($object)
    {
        $properties = array();
        $reflector = new ReflectionObject($object);

        while ($reflector) {
            foreach ($reflector->getProperties() as $property) {
                if ($property->isStatic()) {
                    continue;
                }

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

    /**
     * Return a unique key for the current comparison, which can be used to
     * avoid recursion issues.
     *
     * @param object $left  The left value.
     * @param object $right The right value.
     *
     * @return string The unique comparison key.
     */
    protected function objectComparisonStackKey($left, $right)
    {
        $ids = array(
            spl_object_hash($left),
            spl_object_hash($right)
        );
        sort($ids);

        return implode('.', $ids);
    }

    private $objectComparisonStack;
}

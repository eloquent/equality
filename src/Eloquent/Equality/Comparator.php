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
        $this->objectComparisonStack = array();

        return $this->valueEquals($left, $right);
    }

    /**
     * @param mixed $left
     * @param mixed $right
     *
     * @return boolean
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
            if (!$this->valueEquals($value, $right[$key])) {
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
     * @param object $left
     * @param object $right
     *
     * @return string
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

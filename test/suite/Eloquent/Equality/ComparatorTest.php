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

use Phake;
use PHPUnit_Framework_TestCase;
use stdClass;

class ComparatorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_comparator = new Comparator;
    }

    public function equalsData()
    {
        $data = array();

        $data['String comparison equal'] = array('foo', 'foo', true);
        $data['String comparison inequal'] = array('foo', 'bar', false);
        $data['String comparison inequal strict'] = array('0', 0, false);

        $data['Integer comparison equal'] = array(111, 111, true);
        $data['Integer comparison inequal'] = array(111, 222, false);

        $data['Float comparison equal'] = array(1.11, 1.11, true);
        $data['Float comparison inequal'] = array(1.11, 2.22, false);

        $data['Boolean comparison equal'] = array(true, true, true);
        $data['Boolean comparison inequal'] = array(true, false, false);

        $data['Null comparison equal'] = array(null, null, true);
        $data['Null comparison inequal'] = array(null, 0, false);

        $data['Array comparison equal'] = array(
            array('foo', 'bar'),
            array('foo', 'bar'),
            true
        );
        $data['Array comparison inequal'] = array(
            array('foo', 0),
            array('foo', null),
            false
        );
        $data['Array comparison inequal (differing keys)'] = array(
            array('foo', 'bar'),
            array('foo'),
            false
        );
        $data['Array comparison inequal (with non-array)'] = array(
            array('foo', 'bar'),
            'foo',
            false
        );

        $data['Associative array comparison equal'] = array(
            array('foo' => 'bar', 'baz' => 'qux'),
            array('foo' => 'bar', 'baz' => 'qux'),
            true
        );
        $data['Associative array comparison inequal'] = array(
            array('foo' => 'bar', 'baz' => 0),
            array('foo' => 'bar', 'baz' => null),
            false
        );
        $data['Associative array comparison inequal (differing keys)'] = array(
            array('foo' => 'bar', 'baz' => 'qux'),
            array('foo' => 'bar'),
            false
        );

        $objectA = new stdClass;
        $objectA->foo = 'bar';
        $objectA->baz = 0;
        $objectB = new stdClass;
        $objectB->foo = 'bar';
        $objectB->baz = 0;
        $objectC = new stdClass;
        $objectC->foo = 'bar';
        $objectC->baz = null;
        $objectD = new stdClass;
        $objectD->foo = 'bar';
        $data['Simple object comparison equal'] = array(
            $objectA,
            $objectB,
            true
        );
        $data['Simple object comparison inequal'] = array(
            $objectA,
            $objectC,
            false
        );
        $data['Simple object comparison inequal (differing properties)'] = array(
            $objectA,
            $objectD,
            false
        );
        $data['Simple object comparison inequal (with non-object)'] = array(
            $objectA,
            'foo',
            false
        );

        $objectA = new TestFixture\ChildObject('foo', 0);
        $objectB = new TestFixture\ChildObject('foo', 0);
        $objectC = new TestFixture\ChildObject('foo', null);
        $objectD = new TestFixture\ParentObject('foo', 0);
        $data['Object comparison equal'] = array(
            $objectA,
            $objectB,
            true
        );
        $data['Object comparison inequal'] = array(
            $objectA,
            $objectC,
            false
        );
        $data['Object comparison inequal (differing class)'] = array(
            $objectA,
            $objectD,
            false
        );

        $objectA = new TestFixture\ChildObject('foo', array('foo', 0));
        $objectB = new TestFixture\ChildObject('foo', array('foo', 0));
        $objectC = new TestFixture\ChildObject('foo', array('foo', null));
        $data['Nested comparison equal (array inside object)'] = array(
            $objectA,
            $objectB,
            true
        );
        $data['Nested comparison inequal (array inside object)'] = array(
            $objectA,
            $objectC,
            false
        );

        $objectA = new TestFixture\ChildObject('foo', 0);
        $objectB = new TestFixture\ChildObject('foo', 0);
        $objectC = new TestFixture\ChildObject('foo', null);
        $data['Nested comparison equal (object inside array)'] = array(
            array('bar', $objectA),
            array('bar', $objectB),
            true
        );
        $data['Nested comparison inequal (object inside array)'] = array(
            array('bar', $objectA),
            array('bar', $objectC),
            false
        );

        $objectA = new stdClass;
        $objectA->foo = $objectA;
        $objectB = new stdClass;
        $objectB->foo = $objectB;
        $objectC = new stdClass;
        $objectC->foo = new TestFixture\ChildObject('bar', 0);
        $objectC->baz = $objectC;
        $data['Recursive object reference equal'] = array(
            array('bar', $objectA),
            array('bar', $objectB),
            true
        );
        $data['Recursive object reference inequal'] = array(
            array('bar', $objectA),
            array('bar', $objectC),
            false
        );

        $objectA = new stdClass;
        $objectB = new stdClass;
        $objectB->foo = $objectA;
        $objectA->foo = $objectB;
        $objectC = new stdClass;
        $objectD = new stdClass;
        $objectC->foo = $objectD;
        $objectD->foo = new TestFixture\ChildObject('bar', 0);
        $data['Recursive object reference equal (cross reference)'] = array(
            array('bar', $objectA),
            array('bar', $objectB),
            true
        );
        $data['Recursive object reference inequal (cross reference)'] = array(
            array('bar', $objectC),
            array('bar', $objectD),
            false
        );

        return $data;
    }

    /**
     * @dataProvider equalsData
     */
    public function testEquals($left, $right, $expected)
    {
        $this->assertSame($expected, $this->_comparator->equals($left, $right));
        $this->assertSame($expected, $this->_comparator->equals($right, $left));
    }

    public function testEqualsEqualityComparable()
    {
        $equalityComparable = Phake::mock(
            __NAMESPACE__ . '\EqualityComparable'
        );
        Phake::when($equalityComparable)
            ->isEqualTo(Phake::anyParameters())
            ->thenReturn(true)
        ;

        $this->assertTrue($this->_comparator->equals(
            $equalityComparable,
            'foo'
        ));
        $this->assertTrue($this->_comparator->equals(
            'bar',
            $equalityComparable
        ));
        Phake::inOrder(
            Phake::verify($equalityComparable)->isEqualTo(
                'foo',
                $this->_comparator
            ),
            Phake::verify($equalityComparable)->isEqualTo(
                'bar',
                $this->_comparator
            )
        );
    }
}

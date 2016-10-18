<?php

namespace TestMocks;

use Microbe\Traits\ModelAutoCreatableTrait;
use Microbe\Traits\ModelWalkableTrait;
use TestMocks\Base\TestModel;

/*
 *
 */
class Bar extends TestModel
{

    /*
     *
     */
    protected static
        $MicConnection  = 'db',
        $MicSchema      = 'public',
        $MicTable       = 'test_bar',
        $MicRelations   = [
            'Foo'           => [
                self::ONE,
                true,
                'barId',
                '\\TestMocks\\Foo'
            ],
            'FooFoo'        => [
                self::ONE,
                true,
                'barId',
                '\\TestMocks\\FooFoo'
            ],
            'Moo'           => [
                self::ALL,
                true,
                'barId',
                '\\TestMocks\\Moo'
            ],
            'MooMoo'        => [
                self::ALL,
                true,
                'barId',
                '\\TestMocks\\MooMoo'
            ],
        ],
        $MicValidators  = [
            'a'             => [
                'a'             => [ 'isInt', '#' => 'Must be a number.' ],
                'c'             => [ 'asIntPositive' ],
                'b'             => [ 'asIntRange', 3, 5 ],
                'd'             => [ 'asObj', [ 'a' => 1 ] ]
            ],
            'b'             => [
                'a'             => [ 'isArr', '#' => 'Must be an array.', '&' => [ 'isArrKey', 'b', '#' => 'Must contain "b" key.', '|' => [ 'isArrKey', 'd', '#' => 'Must contain "d" key.' ] ] ],
                'b'             => [ 'isString', '#' => 'Must be a string.' ],
                'c'             => [ 'asArrKey', [ 1, 2, 3 ], 1 ],
                //'d'             => [ 'onArr' ]
            ]
        ];

    /*
     *
     */
    protected static
        $a;

    protected static
        $b;

    protected static
        $c;

    protected static
        $d;
}

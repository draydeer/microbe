<?php

namespace TestMocks;

use TestMocks\Base\TestModel;

/*
 *
 */
class Foo extends TestModel
{

    /*
     *
     */
    protected static
        $MicConnection  = 'dbFoo',
        $MicSchema      = 'public',
        $MicTable       = 'test_foo',
        $MicRelations   = [
            'Bar' => [
                self::OWN,
                'barId',
                true,
                '\\TestMocks\\Bar'
            ],
            'FooFoo' => [
                self::ONE,
                'barId',
                'barId',
                '\\TestMocks\\FooFoo'
            ]
        ];
}

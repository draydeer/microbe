<?php

namespace TestMocks;

use TestMocks\Base\TestModel;

/*
 *
 */
class FooFoo extends TestModel
{

    /*
     *
     */
    protected static
        $MicConnection  = 'dbFooFoo',
        $MicSchema      = 'public',
        $MicTable       = 'test_foo_foo',
        $MicRelations   = [
            'Bar'           => [
                self::OWN,
                'barId',
                true,
                '\\TestMocks\\Bar'
            ]
        ];
}

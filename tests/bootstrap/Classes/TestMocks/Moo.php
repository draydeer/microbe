<?php

namespace TestMocks;

use TestMocks\Base\TestModel;

/*
 *
 */
class Moo extends Foo
{

    /*
     *
     */
    protected static
        $MicConnection  = 'dbMoo',
        $MicSchema      = 'public',
        $MicTable       = 'test_moo',
        $MicRelations   = [
            'Bar'           => [
                self::OWN,
                'barId',
                true,
                '\\TestMocks\\Bar'
            ]
        ];
}

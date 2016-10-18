<?php

namespace TestMocks;

use TestMocks\Base\TestModel;

/*
 *
 */
class MooMoo extends Foo
{

    /*
     *
     */
    protected static
        $MicConnection  = 'dbMooMoo',
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

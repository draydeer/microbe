<?php

namespace TestMocks;

use Microbe\Traits\ModelWalkableTrait;

/*
 *
 */
class Model extends Bar
{
    use ModelWalkableTrait;

    /**
     *
     */
    public function initialize()
    {
        static::setOne(
            'ModelFoo',
            'model_id',
            '\\TestMocks\\ModelFoo'
        );
    }
}

<?php

namespace Test;

use Microbe\Microbe;
use Microbe\MicrobeCollectionBulk;
use Microbe\MicrobeConnection;
use TestMocks\Bar;
use TestMocks\Connections\Connection;
use TestMocks\BarFake;
use TestMocks\Foo;
use TestMocks\FooFoo;
use TestMocks\Moo;
use TestMocks\MooMoo;
use VSP\Aux\Origin\Lib\P;

abstract class TestBaseBenchmark extends TestBase
{

    /*
     *
     */
    public function test()
    {
        $this->model = new Bar();

        $this->model->Moo = Moo::getCollectionBulk();

        for ($I = 0; $I < 10000; $I ++) {
            $this->model->Moo->add([
                'a' => $I,
                'b' => 'abc',
                'c' => 2
            ]);
        }

        benchmark('bulk insert');

        $this->model->put();

        benchmark(10000);

        benchmark('fetch all');

        foreach (Moo::all([]) as $i => $_model) {

        }

        benchmark($i + 1);

        benchmark('fetch one');

        for ($i = 0; $i < 100000; $i ++) {
            Moo::one(['a'=>55,'b'=>1]);
        }

        benchmark($i);
    }

}

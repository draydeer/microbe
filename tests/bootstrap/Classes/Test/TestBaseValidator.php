<?php

namespace Test;

/*
 *
 */
abstract class TestBaseValidator extends TestBase
{

    /**
     *
     */
    public function test()
    {
        $this->model = new \TestMocks\Bar();

        $this->model->assign([
            'a' => [ 'd' => 4 ],
            'b' => '56',
            'd' => new \stdClass()
        ]);
    }
}

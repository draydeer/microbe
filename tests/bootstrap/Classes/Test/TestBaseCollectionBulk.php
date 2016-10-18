<?php

namespace Test;

/*
 *
 */
use Microbe\MicrobeCollectionBulk;
use TestMocks\Bar;
use TestMocks\Moo;

abstract class TestBaseCollectionBulk extends TestBase
{

    /*
     *
     */
    public static
        $condition = [];

    /*
     *
     */
    public function testInsert()
    {
        $this->model = new Bar();

        $this->model->Moo = Moo::getCollectionBulk();

        $this->assertInstanceOf('\\Microbe\\MicrobeCollectionBulk', $this->model->Moo);

        for ($I = 0; $I < 11111; $I ++) {
            $this->model->Moo->add([
                'a' => $I,
                'b' => 'abc',
                'c' => 2
            ]);
        }

        $this->model->put();

        $this->assertEquals(true, $this->model->isStable());

        $this->model = Bar::one($this->model->getStateFieldPK());

        $this->assertTrue($this->model->isStable());

        $this->assertInstanceOf('\\Microbe\\MicrobeCollection', $this->model->Moo);
        $this->assertEquals(11111, count($this->model->Moo));

        $this->assertEquals($this->model, $this->model->Moo->getRelationLeft());
    }
}

<?php

namespace Test;

/*
 *
 */
abstract class TestBaseRelations extends TestBase
{

    /*
     *
     */
    public static
        $condition = [];

    /*
     *
     */
    public function testAssignUpdateAll()
    {
        $this->model = new \TestMocks\Bar();

        $this->model->Moo = new \TestMocks\Moo();
        $this->model->Moo = new \TestMocks\Moo();
        $this->model->Moo = new \TestMocks\Moo();

        $this->assertInstanceOf('\\Microbe\\MicrobeCollection', $this->model->Moo);
        $this->assertEquals(3, count($this->model->Moo));

        $this->assertEquals($this->model, $this->model->Moo->getRelationLeft());

        $this->model->put();

        $this->assertTrue($this->model->isStable());

        $this->model = \TestMocks\Bar::one($this->model->getStateFieldPK());

        $this->assertTrue($this->model->isStable());

        $this->assertInstanceOf('\\Microbe\\MicrobeCollection', $this->model->Moo);
        $this->assertEquals(3, count($this->model->Moo));

        $this->assertEquals($this->model, $this->model->Moo->getRelationLeft());
    }

    /*
     *
     */
    public function testAssignUpdateOne()
    {
        $this->model = new \TestMocks\Bar();

        $this->model->Foo = new \TestMocks\Foo();

        $this->assertInstanceOf('\\TestMocks\\Foo', $this->model->Foo);
        $this->assertInstanceOf('\\TestMocks\\Bar', $this->model->Foo->Bar);
        $this->assertEquals($this->model, $this->model->Foo->Bar);

        $this->model->put();

        $this->assertTrue($this->model->isStable());
        $this->assertTrue($this->model->Foo->isStable());
        $this->assertEquals($this->model->getStateFieldPK(), $this->model->Foo->barId);
    }

    /*
     *
     */
    public function testAssignUpdateOwn()
    {
        $this->model = new \TestMocks\Foo();

        $this->model->Bar = new \TestMocks\Bar();

        $this->assertInstanceOf('\\TestMocks\\Bar', $this->model->Bar);
        $this->assertInstanceOf('\\TestMocks\\Foo', $this->model->Bar->Foo);
        $this->assertEquals($this->model, $this->model->Bar->Foo);

        $this->model->put();

        $this->assertTrue($this->model->isStable());
        $this->assertTrue($this->model->Bar->isStable());
        $this->assertEquals($this->model->barId, $this->model->Bar->getStateFieldPK());
    }
}

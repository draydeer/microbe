<?php

namespace Test;

/*
 *
 */
use TestMocks\Bar;

abstract class TestBaseCRUD extends TestBase
{

    /*
     *
     */
    public static
        $condition = [];

    /*
     *
     *
     */
    public function testAbsence()
    {
        $this->assertNull(Bar::one(static::$condition));
    }

    /*
     * @depends testAbsence
     */
    public function testInsert()
    {
        $this->model = new \TestMocks\Bar();

        $this->assertInstanceOf('\\TestMocks\\Bar', $this->model);

        $this->assertEquals($this->model->getDirty(), $this->model->getDirty());
        $this->assertTrue($this->model->isNew());

        $this->model->assign([
            'a' => 1,
            'b' => 'abc'
        ]);

        $this->assertEquals(1, $this->model->a);
        $this->assertEquals('abc', $this->model->b);

        $this->assertEquals(\Microbe\MicrobeModel::DRT_TRANSIENT, $this->model->getDirty());
        $this->assertFalse($this->model->isStable());

        $this->model->put();

        $this->assertEquals(\Microbe\MicrobeModel::DRT_STABLE, $this->model->getDirty());
        $this->assertTrue($this->model->isStable());
    }

    /*
     * @depends testInsert
     */
    public function testUpdate()
    {
        $this->model = \TestMocks\Bar::one(static::$condition);

        $this->assertInstanceOf('\\TestMocks\\Bar', $this->model);

        $this->assertEquals(1, $this->model->a);
        $this->assertEquals('abc', $this->model->b);

        $this->assertEquals(\Microbe\MicrobeModel::DRT_STABLE, $this->model->getDirty());
        $this->assertTrue($this->model->isStable());

        $this->model->assign([
            'a' => 1,
            'b' => 'abc'
        ]);

        $this->assertEquals(\Microbe\MicrobeModel::DRT_TRACKING, $this->model->getDirty());
        $this->assertFalse($this->model->isStable());

        $this->model->put();

        $this->assertEquals(\Microbe\MicrobeModel::DRT_STABLE, $this->model->getDirty());
        $this->assertTrue($this->model->isStable());
    }

    /*
     * @depends testUpdate
     */
    public function testDelete()
    {
        $this->model = \TestMocks\Bar::one(static::$condition);

        $this->assertInstanceOf('\\TestMocks\\Bar', $this->model);

        $this->assertEquals(1, $this->model->a);
        $this->assertEquals('abc', $this->model->b);

        $this->assertEquals(\Microbe\MicrobeModel::DRT_STABLE, $this->model->getDirty());
        $this->assertTrue($this->model->isStable());

        $this->model->del();

        $this->model = \TestMocks\Bar::one(static::$condition);

        $this->assertNull($this->model);
    }

    /*
     * @depends testDelete
     */
    public function testFindAll()
    {
        $this->insBar(55);

        $_result = \TestMocks\Bar::all([]);

        $i = 0;

        foreach ($_result as $i => $v) {

        }

        $this->assertEquals(55, $i + 1);

        \TestMocks\Bar::rem([]);
    }

    /*
     * @depends testDelete
     */
    public function testFindAllChunked()
    {
        $this->insBar(66);

        $_result = \TestMocks\Bar::all([])->chunked(7);

        $i = 0;

        foreach ($_result as $i => $v) {

        }

        $this->assertEquals(66, $i + 1);

        \TestMocks\Bar::rem([]);
    }

    /*
     * @depends testDelete
     */
    public function testFindOne()
    {
        $this->insBar(77);

        $this->assertInstanceof('\\TestMocks\\Bar', \TestMocks\Bar::one());

        $this->assertNull(\TestMocks\Bar::one([ 'aaa' => 444 ]));

        \TestMocks\Bar::rem([]);
    }
}

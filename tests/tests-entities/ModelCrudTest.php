<?php

class ModelCrudTest extends \PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        \Microbe\Microbe::microbe()->setConnection(
            'bar',
            [],
            \Microbe\Microbe::MEMORY,
            []
        );

        \Microbe\Microbe::microbe()->setConnection(
            'foo',
            [],
            \Microbe\Microbe::MEMORY,
            []
        );

        \Microbe\Microbe::microbe()->setConnection(
            'moo',
            [],
            \Microbe\Microbe::MEMORY,
            []
        );
    }

    public function testShouldByConditionReturnNullWhenNotExists()
    {
        $this->assertNull(\Mocks\Bar::one(['a' => 1]));
    }

    public function testShouldInsertThenHaveInitialAndStableStateAndPrimaryKey()
    {
        $model = \Mocks\Bar::create(['a' => 1]);

        $this->assertTrue($model->put());

        $this->assertEquals(1, $model->a);

        $this->assertTrue($model->isStable());

        $this->assertTrue($model->getStateFieldPK() !== null);
    }

    public function testShouldInsertThenBeSameEntityWithStableStateWhenSelected()
    {
        $model = \Mocks\Bar::create(['a' => 1]);

        $this->assertTrue($model->put());

        $modelSelected = \Mocks\Bar::one(['a' => 1]);

        $this->assertTrue($modelSelected instanceof \Mocks\Bar);

        $this->assertTrue($modelSelected->isStable());
    }

    public function testShouldSelectThenHaveDirtyTrackingStateWhenSetStateField()
    {
        $model = \Mocks\Bar::create(['a' => 1]);

        $this->assertTrue($model->put());

        $modelSelected = \Mocks\Bar::one(['a' => 1]);

        $modelSelected->a = 2;

        $this->assertEquals(\Mocks\Bar::DRT_TRACKING, $modelSelected->getDirty());
    }

    public function testShouldSelectThenSetStateFieldThenHaveStableStateWhenUpdated()
    {
        $model = \Mocks\Bar::create(['a' => 2]);

        $this->assertTrue($model->put());

        $modelSelected = \Mocks\Bar::one(['a' => 2])->assign(['a' => 3]);

        $modelSelected->put();

        $this->assertTrue($modelSelected->isStable());
    }

    public function testShouldDelete()
    {
        $model = \Mocks\Bar::create(['a' => 2]);

        $this->assertTrue($model->put());

        $id = $model->getStateFieldPK();

        $model->del();

        $this->assertNull(\Mocks\Bar::one($id));
    }

}

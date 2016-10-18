<?php

class ModelBasicsTest extends \PHPUnit_Framework_TestCase
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

    public function testShouldReturnConnectionAdapter()
    {
        $this->assertTrue(\Mocks\Bar::getConnection() instanceof \Microbe\Adapters\AdapterMemory);
    }

    public function testShouldHaveNewStateWhenCreatedAsNew()
    {
        $this->assertTrue(\Mocks\Bar::create()->isNew());
    }

    public function testShouldHaveNoStableStateWhenCreatedAsNew()
    {
        $this->assertFalse(\Mocks\Bar::create()->isStable());
    }

    public function testShouldSetProvidedStateThenHaveItWhenCreatedAsNew()
    {
        $model = \Mocks\Bar::create(['a' => 1, 'b' => 2, 'c' => 3]);

        $this->assertEquals(1, $model->a);

        $this->assertEquals(2, $model->b);

        $this->assertEquals(3, $model->c);
    }

    public function testShouldGetNullWhenValueNotExists()
    {
        $this->assertNull(\Mocks\Bar::create()->a);
    }

    public function testShouldSetStateFieldValueThenReturnIt()
    {
        $this->assertEquals(1, \Mocks\Bar::create()->a = 1);
    }

    public function testShouldSetStateFieldValueThenHaveIt()
    {
        $model = \Mocks\Bar::create();

        $model->a = 1;

        $this->assertEquals(1, $model->a);
    }

    public function testShouldAssignStateFieldValuesThenHaveIt()
    {
        $model = \Mocks\Bar::create();

        $model->assign(['a' => 1]);

        $this->assertEquals(1, $model->a);
    }

}

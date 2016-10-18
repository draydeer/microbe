<?php

class ExtGetValueTraitTest extends \PHPUnit_Framework_TestCase
{
    use \Microbe\Traits\Ext\GetValueTrait;

    public function testShouldReturnDefaultValue()
    {
        $stub = [];

        $this->assertEquals(123, static::getValue($stub, 'a', 123));

        $stub = (object) [];

        $this->assertEquals(123, static::getValue($stub, 'a', 123));

        $stub = 'abc';

        $this->assertEquals(123, static::getValue($stub, 'a', 123));
    }

    public function testShouldReturnValueFromArray()
    {
        $stub = ['a' => 333];

        $this->assertEquals(333, static::getValue($stub, 'a', 123));
    }

    public function testShouldReturnValueFromObject()
    {
        $stub = (object) ['a' => 333];

        $this->assertEquals(333, static::getValue($stub, 'a', 123));
    }

}

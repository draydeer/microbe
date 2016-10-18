<?php

class ExtWhoAmITraitTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldGetCurrentClassname()
    {
        $this->assertEquals(\Stubs\ClassA::whoAmI(), 'ClassA');
    }

    public function testShouldGetClassnameOfInstance()
    {
        $this->assertEquals('ClassA', \Stubs\ClassA::whoIs('\\Stubs\\ClassA'));

        $this->assertEquals('ClassA', \Stubs\ClassA::whoIs(new \Stubs\ClassA()));
    }

    public function testShouldGetCurrentNamespace()
    {
        $this->assertEquals('Stubs', \Stubs\ClassA::whoIsMyNS());
    }

    public function testShouldGetNamespaceOfInstance()
    {
        $this->assertEquals('Stubs', \Stubs\ClassA::whoIsNS('Stubs\\ClassA'));

        $this->assertEquals('Stubs', \Stubs\ClassA::whoIsNS(new \Stubs\ClassA()));
    }

    public function testShouldGetCurrentClassnameWithOffset()
    {
        $this->assertEquals('\\ClassA', \Stubs\ClassA::whoAmI(0));
    }

    public function testShouldGetCurrentNamespaceWithOffset()
    {
        $this->assertEquals('Stubs\\', \Stubs\ClassA::whoIsMyNS(1));
    }

}

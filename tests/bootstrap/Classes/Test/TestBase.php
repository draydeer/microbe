<?php

namespace Test;

use Microbe\Microbe;
use Microbe\MicrobeModel;
use TestMocks\Bar;
use TestMocks\Foo;
use TestMocks\FooFoo;
use TestMocks\Moo;
use TestMocks\MooMoo;

/**
 * Class TestBase
 * @package Test
 */
abstract class TestBase extends \PHPUnit_Framework_TestCase
{

    /** @var MicrobeModel $ */
    public $model;

    /*
     *
     */
    public static function setUpBeforeClass()
    {
        Microbe::microbe()->setConnection(
            'db',
            [
                'host' => 'localhost',
                'name' => 'microbe_test_db',
                'user' => 'test',
                'pass' => 'test'
            ],
            Microbe::MYSQL
        );
        Microbe::microbe()->setConnection(
            'dbFoo',
            [
                'host' => 'localhost',
                'name' => 'microbe_test_db',
                'user' => null,
                'pass' => null
            ],
            Microbe::MONGO
        );
        Microbe::microbe()->setConnection(
            'dbFooFoo',
            [
                'host' => 'localhost',
                'name' => 'microbe_test_db',
                'user' => 'test',
                'pass' => 'test'
            ],
            Microbe::MYSQL
        );
        Microbe::microbe()->setConnection(
            'dbMoo',
            [
                'host' => 'localhost',
                'name' => 'microbe_test_db',
                'user' => 'test',
                'pass' => 'test'
            ],
            Microbe::MYSQL
        );
        Microbe::microbe()->setConnection(
            'dbMooMoo',
            [
                'host' => 'localhost',
                'name' => 'microbe_test_db',
                'user' => null,
                'pass' => null
            ],
            Microbe::MONGO
        );
    }

    public static function tearDownAfterClass()
    {
        Bar::rem([]);
        Foo::rem([]);
        FooFoo::rem([]);
        Moo::rem([]);
        MooMoo::rem([]);
    }

    /*
     *
     */
    public function insBar($count)
    {
        for (; $count > 0; $count --) {
            $a = new Bar();

            $a->assign([
                'a' => $count,
                'b' => 'abc',
                'c' => 3
            ]);

            $a->put();
        }
    }

    /*
     *
     */
    public function insBarFoo($count)
    {
        for (; $count > 0; $count --) {
            $a = new Bar();

            $a->assign([
                'a' => $count,
                'b' => 'abc',
                'c' => 3
            ]);

            $a->Foo = new Foo();

            $a->Foo->assign([
                'a' => $count,
                'b' => 'foo',
                'c' => 2
            ]);

            $a->put();
        }
    }

    /*
     *
     */
    public function insBarFooFoo($count)
    {
        for (; $count > 0; $count --) {
            $a = new Bar();

            $a->assign([
                'a' => $count,
                'b' => 'abc',
                'c' => 3
            ]);

            $a->Foo = new Foo();

            $a->Foo->assign([
                'a' => $count,
                'b' => 'foo',
                'c' => 2
            ]);

            $a->FooFoo = new FooFoo();

            $a->FooFoo->assign([
                'a' => $count,
                'b' => 'foofoo',
                'c' => 5
            ]);

            $a->put();
        }
    }
}

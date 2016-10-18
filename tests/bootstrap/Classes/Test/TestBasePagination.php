<?php

namespace Test;

/*
 *
 */
abstract class TestBasePagination extends TestBase
{

    /*
     *
     */
    public static
        $condition = [];

    /*
     *
     */
    public function test()
    {
        $this->insBar(12);

        $res = \TestMocks\Bar::all([])->paginate(
            1,
            5
        );

        $this->assertEquals(5, count($res->items));

        $res = \TestMocks\Bar::all([])->paginate(
            2,
            10
        );

        $this->assertEquals(2, count($res->items));

        $res = \TestMocks\Bar::all([])->paginate(
            3,
            5
        );

        $this->assertEquals(2, count($res->items));
    }

    /*
     *
     */
    public function testQuery()
    {
        $this->insBarFoo(12);

        $_query = \TestMocks\Bar::getQuery();

        $_query->joinIn('Foo');

        $res = $_query->query([])->paginate(
            1,
            5
        );

        $this->assertEquals(5, count($res->items));

        $res = $_query->query([])->paginate(
            2,
            10
        );

        $this->assertEquals(2, count($res->items));

        $res = $_query->query([])->paginate(
            3,
            5
        );

        $this->assertEquals(2, count($res->items));

        foreach ($res->items as $I => $V) {
            $this->assertArrayHasKey('Bar', $V);
            $this->assertArrayHasKey('Foo', $V);
        }
    }
}

<?php

namespace Test;

use Microbe\Microbe;
use Microbe\MicrobeConnection;
use TestMocks\Connections\Connection;
use TestMocks\BarFake;
use VSP\Aux\Origin\Lib\P;

abstract class TestBaseFake extends TestBase
{

    /** @var MicrobeConnection $connection */
    public $connection;

    /*
     *
     */
    public function setUp()
    {

    }

    /*
     *
     */
    public function testConnectionException()
    {
        Microbe::microbe()->setConnection('dbFake', $this->connection = Connection::create(Microbe::microbe(), [
            'name' => 'fake',
            'host' => 'fake',
            'user' => 'fake',
            'pass' => 'fake'
        ]));

        $this->setExpectedException('\\Microbe\\Exceptions\\ConnectionException');

        $_flag = false;

        \Microbe\Microbe::microbe()->setExceptionHandlerOnConnection(
            function (\Exception $e, $reason, $adapter) use (& $_flag) {
                $_flag = true;

                $this->assertEquals(Microbe::microbe()->getConnection('dbFake')->getAdapter(), $adapter);

                return false;
            }
        );

        $this->assertFalse($_flag);

        BarFake::one([ 'a' => 1 ]);

        $this->assertTrue($_flag, 'Handler was not called!');
    }

    /*
     * @depends testConnectionException
     */
    public function testRequestExceptionOne()
    {
        Microbe::microbe()->getConnection('dbFake')->configure([
            'host' => 'localhost',
            'name' => 'microbe_test_db',
            'user' => 'test',
            'pass' => 'test'
        ]);

        $this->setExpectedException('\\Microbe\\Exceptions\\RequestException');

        $_flag = false;

        \Microbe\Microbe::microbe()->setExceptionHandlerOnRequest(
            function (\Exception $e, $reason, $adapter) use (& $_flag) {
                $_flag = true;

                $this->assertEquals(Microbe::microbe()->getConnection('dbFake')->getAdapter(), $adapter);

                return false;
            }
        );

        $this->assertFalse($_flag);

        BarFake::one([ 'a' => 1 ]);

        $this->assertTrue($_flag, 'Handler was not called!');
    }

    /*
 * @depends testConnectionException
 */
    public function testRequestExceptionAll()
    {
        Microbe::microbe()->getConnection('dbFake')->configure([
            'host' => 'localhost',
            'name' => 'microbe_test_db',
            'user' => 'test',
            'pass' => 'test'
        ]);

        $this->setExpectedException('\\Microbe\\Exceptions\\RequestException');

        $_flag = false;

        \Microbe\Microbe::microbe()->setExceptionHandlerOnRequest(
            function (\Exception $e, $reason, $adapter) use (& $_flag) {
                $_flag = true;

                $this->assertEquals(Microbe::microbe()->getConnection('dbFake')->getAdapter(), $adapter);

                return false;
            }
        );

        $this->assertFalse($_flag);

        P::run(BarFake::all([ 'a' => 1 ]));

        $this->assertTrue($_flag, 'Handler was not called!');
    }

    /*
     * @depends testConnectionException
     */
    public function testRequestExceptionAllChunked()
    {
        Microbe::microbe()->getConnection('dbFake')->configure([
            'host' => 'localhost',
            'name' => 'microbe_test_db',
            'user' => 'test',
            'pass' => 'test'
        ]);

        $this->setExpectedException('\\Microbe\\Exceptions\\RequestException');

        $_flag = false;

        \Microbe\Microbe::microbe()->setExceptionHandlerOnRequest(
            function (\Exception $e, $reason, $adapter) use (& $_flag) {
                $_flag = true;

                $this->assertEquals(Microbe::microbe()->getConnection('dbFake')->getAdapter(), $adapter);

                return false;
            }
        );

        $this->assertFalse($_flag);

        P::run(BarFake::all([ 'a' => 1 ])->chunked(5));

        $this->assertTrue($_flag, 'Handler was not called!');
    }
}

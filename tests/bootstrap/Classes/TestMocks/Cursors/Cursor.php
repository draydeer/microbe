<?php

namespace TestMocks\Cursors;

use Microbe\Exceptions\CursorFetchException;
use Microbe\MicrobeCursor;

/**
 * Class Cursor
 * @package TestMocks\Adapters
 */
class Cursor extends MicrobeCursor
{

    /**
     *
     */
    public static function getFetchable($mixed, $timeout = 0)
    {
        throw new \PDOException('Test');
    }
}

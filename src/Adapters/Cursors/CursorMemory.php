<?php

namespace Microbe\Adapters\Cursors;

use Microbe\Exceptions\CursorFetchException;
use Microbe\MicrobeCursor;

/**
 * Class CursorMemory
 * @package Microbe\Adapters\Cursors
 */
class CursorMemory extends MicrobeCursor
{

    /**
     * Get fetchable cursor.
     *
     * @param mixed $mixed Cursor.
     * @param int $timeout Cursor timeout.
     *
     * @return \Generator
     */
    public static function getFetchable($mixed, $timeout = 0)
    {
        $i = 0;

        foreach ($mixed as $v) {
            yield $i ++ => $v;
        }
    }

}

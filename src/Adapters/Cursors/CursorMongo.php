<?php

namespace Microbe\Adapters\Cursors;

use Microbe\Exceptions\CursorFetchException;
use Microbe\MicrobeCursor;

/**
 * Class CursorMongo
 * @package Microbe\Adapters\Cursors
 */
class CursorMongo extends MicrobeCursor
{

    /**
     * Get fetchable cursor.
     *
     * @param \MongoCursor $mixed Cursor.
     * @param int $timeout Cursor timeout.
     *
     * @return \Generator
     */
    public static function getFetchable($mixed, $timeout = 0)
    {
        if ($timeout > 0) {
            $mixed->timeout($timeout * 1000);
        }

        $i = 0;

        foreach ($mixed as $v) {
            yield $i ++ => $v;
        }
    }

}

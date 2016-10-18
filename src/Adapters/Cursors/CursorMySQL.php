<?php

namespace Microbe\Adapters\Cursors;

use Microbe\Exceptions\CursorFetchException;
use Microbe\MicrobeCursor;

/**
 * Class CursorMySQL
 * @package Microbe\Adapters\Cursors
 */
class CursorMySQL extends MicrobeCursor
{

    /**
     * Get fetchable cursor.
     *
     * @param \PDOStatement $mixed Cursor.
     * @param int $timeout Cursor timeout.
     *
     * @return \Generator
     */
    public static function getFetchable($mixed, $timeout = 0)
    {
        if ($timeout) {

        }

        $i = 0;

        while ($v = $mixed->fetch(\PDO::FETCH_ASSOC)) {
            yield $i ++ => $v;
        }
    }

}

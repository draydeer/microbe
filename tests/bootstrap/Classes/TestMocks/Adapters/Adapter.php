<?php

namespace TestMocks\Adapters;

use Microbe\Adapters\AdapterMySQL;
use Microbe\MicrobeModelMetadata;
use TestMocks\Cursors\Cursor;

/**
 * Class Adapter
 * @package TestMocks\Adapters
 */
class Adapter extends AdapterMySQL
{

    /**
     *
     */
    public static function getCursor(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        callable $f = null
    )
    {
        return $f ? new Cursor($metadata, $param, $extra, $f) : new Cursor($metadata, $param, $extra);
    }

    /**
     *
     */
    public static function getFetchable($cursor, $timeout = 0)
    {
        return Cursor::getFetchable($cursor, $timeout);
    }
}

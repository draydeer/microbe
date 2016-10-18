<?php

namespace Microbe\Adapters\ExceptionClasses;

/**
 * Class Mongo
 * @package Microbe\Adapters\ExceptionClasses
 */
class Mongo
{

    /**
     *
     */
    public static function examine(\Exception $e)
    {
        switch (true) {
            case $e instanceof \MongoConnectionException:
                return 'connection';

            case $e instanceof \MongoCursorException:
                return 'cursor';

            case $e instanceof \MongoCursorTimeoutException:
                return 'cursorTimeout';

            case $e instanceof \MongoDuplicateKeyException:
                return 'dmoDuplicate';

            case $e instanceof \MongoWriteConcernException:
                return 'shard';

            case $e instanceof \MongoExecutionTimeoutException:
                return 'timeout';

            default:
                return null;
        }
    }

}

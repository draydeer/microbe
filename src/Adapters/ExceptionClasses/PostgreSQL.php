<?php

namespace Microbe\Adapters\ExceptionClasses;

/**
 * Class PostgreSQL
 * @package Microbe\Adapters\ExceptionClasses
 */
class PostgreSQL
{

    /**
     *
     */
    public static function examine(\Exception $e)
    {
        switch (true) {
            case $e instanceof \PDOException:
                switch ($e->getCode()) {

                }

                return null;

            default:
                return null;
        }
    }

}

<?php

namespace Microbe\Adapters\ExceptionClasses;
use RSE\RSEDimYt\Models\listToken;

/**
 * Class Mysql
 * @package Microbe\Adapters\ExceptionClasses
 */
class Mysql
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

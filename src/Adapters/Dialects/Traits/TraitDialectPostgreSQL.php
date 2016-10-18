<?php

namespace Microbe\Adapters\Dialects\Traits;

/**
 * Class TraitDialectPostgreSQL
 * @package Microbe\Adapters\Dialects\Traits
 */
trait TraitDialectPostgreSQL
{

    /**
     *
     */
    public static function getDialectForEOL()
    {
        return 'TRUE';
    }

    /**
     *
     */
    public static function getDialectForLIM($l, $o)
    {
        return $l ? 'LIMIT ' . $l . ' OFFSET ' . $o : 'OFFSET ' . $o;
    }

    /**
     *
     */
    public static function getDialectForLastInsertID($alias)
    {
        return $alias . '_seq';
    }
}

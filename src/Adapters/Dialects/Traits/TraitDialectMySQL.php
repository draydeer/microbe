<?php

namespace Microbe\Adapters\Dialects\Traits;

/**
 * Class TraitDialectMySQL
 * @package Microbe\Adapters\Dialects\Traits
 */
trait TraitDialectMySQL
{

    /**
     *
     */
    public static function getDialectForEOL()
    {
        return '1';
    }

    /**
     *
     */
    public static function getDialectForLIM($l, $o)
    {
        return $l ? 'LIMIT ' . $o . ',' . $l : 'OFFSET ' . $o;
    }

    /**
     *
     */
    public static function getDialectForLastInsertID($alias)
    {
        return null;
    }

    /**
     *
     */
    public static function getBindingKeyValueFromKeys($map)
    {
        $result = '';

        foreach ($map as $k => $v) {
            $result .= "$k=:$k,";
        }

        return rtrim($result, ',');
    }
}

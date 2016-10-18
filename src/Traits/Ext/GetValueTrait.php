<?php

namespace Microbe\Traits\Ext;

/**
 * Class GetValueTrait
 * @package Microbe\Traits\Ext
 */
trait GetValueTrait
{

    /**
     * Get value by key.
     *
     * @param array|object $container Container array or object.
     * @param string $key Key.
     * @param mixed $defaultValue Default value to return if key not exists.
     *
     * @return mixed|null
     */
    public static function getValue($container, $key, $defaultValue = null)
    {
        if (is_array($container) || $container instanceof \ArrayAccess) {
            return isset($container[$key]) ? $container[$key] : $defaultValue;
        }

        if (is_object($container)) {
            return isset($container->{$key}) ? $container->{$key} : $defaultValue;
        }

        return $defaultValue;
    }

}

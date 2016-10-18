<?php

namespace Microbe\Traits\Ext;

/**
 * Class GetInstanceTrait
 * @package Microbe\Traits\Ext
 */
trait GetInstanceTrait
{

    /** @var mixed $_inInstance */
    private static $_inInstance;

    /**
     * Get new instance.
     *
     * @return static
     */
    public static function getInstance($param = null, $class = null)
    {
        $result = new \ReflectionClass($class === null ? get_called_class() : $class);

        return $result->newInstanceArgs(
            is_array($param) ? $param : func_get_args()
        );
    }

    /**
     * Get shared instance.
     *
     * @return mixed
     */
    public static function getInstanceShared()
    {
        if (self::$_inInstance === null) {
            return self::$_inInstance = static::getInstance(func_get_args(), get_called_class());
        }

        return self::$_inInstance;
    }

    /**
     * Set shared instance.
     *
     * @return mixed
     */
    public static function setInstanceShared()
    {
        return self::$_inInstance = static::getInstance(func_get_args(), get_called_class());
    }

    /**
     * Wrapper of static::getInstanceShared
     *
     * @return mixed
     */
    public static function getStatic()
    {
        return static::getInstanceShared();
    }

}

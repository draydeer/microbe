<?php

namespace Microbe\Traits\Ext;

/**
 * Class WhoAmITrait
 * @package Microbe\Traits\Ext
 */
trait WhoAmITrait
{

    /** @var null|string $WhoAmI */
    protected static $WhoAmI = null;

    /** @var null|string $WhoIsMyNS */
    protected static $WhoIsMyNS = null;

    /**
     * Get current class name.
     *
     * @param int $offset
     *
     * @return string
     */
    public static function whoAmI($offset = 1)
    {
        $string = get_called_class();
        $strPos = strrpos($string, '\\');

        if ($strPos !== false) {
            $string = substr($string, $strPos + $offset);
        }

        return self::$WhoAmI = $string;
    }

    /**
     * Get class name of scope class.
     *
     * @param string $scope Class path or instance.
     *
     * @return string
     */
    public static function whoIs($scope)
    {
        $string = is_object($scope) ? get_class($scope) : $scope;
        $strPos = strrpos($string, '\\');

        if ($strPos !== false) {
            return substr($string, $strPos + 1);
        }

        return $string;
    }

    /**
     * Get current namespace.
     *
     * @param int $offset
     *
     * @return string
     */
    public static function whoIsMyNS($offset = 0)
    {
        $string = get_called_class();
        $strPos = strrpos($string, '\\');

        if ($strPos !== false) {
            return self::$WhoIsMyNS = substr($string, 0, $strPos + $offset);
        }

        return null;
    }

    /**
     * Get namespace of scope class.
     *
     * @param string $scope Class path or instance.
     *
     * @return string
     */
    public static function whoIsNS($scope)
    {
        $string = is_object($scope) ? get_class($scope) : $scope;
        $strPos = strrpos($string, '\\');

        if ($strPos !== false) {
            return substr($string, 0, $strPos);
        }

        return null;
    }

}

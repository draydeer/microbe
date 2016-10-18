<?php

namespace Microbe;

use Microbe\Traits\Ext\WhoAmITrait;

/**
 * Class MicrobeBase
 * @package Microbe
 */
abstract class MicrobeBase
{
    use WhoAmITrait;

    /** @var Microbe $_microbe */
    protected $_microbe;

    /**
     * Get [Microbe] instance.
     *
     * @return Microbe
     */
    public static function microbe()
    {
        return Microbe::getInstanceShared();
    }

    /**
     *
     */
    public static function getParam($v, $k, $d = null)
    {
        return is_array($v) ? (isset($v[$k]) ? $v[$k] : $d) : (is_object($v) ? (isset($v->{$k}) ? $v->{$k} : $d) : $d);
    }

    /**
     * Get [Microbe] instance.
     *
     * @return Microbe
     */
    public function getMicrobe()
    {
        return $this->_microbe ? $this->_microbe : $this->_microbe = static::microbe();
    }

}

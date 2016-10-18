<?php

namespace Microbe;

use Microbe\Exceptions\NotImplementedException;
use Microbe\Traits\Ext\WhoAmITrait;

/**
 * Class MicrobeCriteria
 * @package Microbe
 */
abstract class MicrobeCriteria
{
    use WhoAmITrait;

    /*
     *
     */
    protected
        $_criteria,
        $_criteriaRef,
        $_i = 0,
        $_l,
        $_r;

    /**
     *
     */
    public static function compileParametrized(
        $alias,
      & $param,
        $forceQuery = false,
        $pk = null,
      & $paramBind = []
    )
    {
        return $param;
    }

    /**
     *
     */
    public function __construct($l, $r = null)
    {
        $this->clear();

        $this->setL($l);
        $this->setR($r);
    }

    /**
     * @return null
     */
    public function getCondition()
    {
        throw new NotImplementedException('On condition for:' . static::whoAmI());
    }

    /**
     *
     */
    public function setL($value)
    {
        $this->_l = $value;
    }

    /**
     *
     */
    public function setR($value)
    {
        $this->_r = $value;
    }

    /**
     *
     */
    public function clear()
    {
        $this->_criteria = [ [] ];
        $this->_criteriaRef =&$this->_criteria[0];
        $this->_i = 0;

        return $this;
    }

    /**
     *
     */
    public function _an()
    {
        return $this;
    }

    /**
     *
     */
    public function _eq($l, $r = null)
    {

    }

    /**
     *
     */
    public function _ge($l, $r = null)
    {

    }

    /**
     *
     */
    public function _gt($l, $r = null)
    {

    }

    /**
     *
     */
    public function _le($l, $r = null)
    {

    }

    /**
     *
     */
    public function _lt($l, $r = null)
    {

    }

    /**
     *
     */
    public function _ne($l, $r = null)
    {

    }

    /**
     *
     */
    public function _or()
    {
        if (count($this->_criteriaRef)) {
            $this->_criteria[] = [];
            $this->_criteriaRef =&$this->_criteria[++ $this->_i];
        }

        return $this;
    }

    /**
     *
     */
    public function _ro()
    {

    }
}

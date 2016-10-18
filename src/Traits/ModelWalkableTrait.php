<?php

namespace Microbe\Traits;

use Microbe\MicrobeModel;
use Microbe\MicrobeObject;

/**
 * Class ModelWalkableTrait
 * @package Microbe\Traits
 */
trait ModelWalkableTrait
{

    /**
     *
     */
    public function getStateField($k, $d = null)
    {
        $k.= '.';
        $i = 0;
        $K =&$this->_state;

        while(($P = strpos($k, '.', $i)) !== false) {
            $i = substr($k, $i, $P - $i);

            if (is_array($K) || $K instanceof \ArrayAccess) {
                if (isset($K[$i])) {
                    $K =&$K[$i];
                } else {
                    return $d;
                }
            } else
            if (is_object($K)) {
                if (isset($K->{ $i })) {
                    $K =&$K->{ $i };
                } else {
                    return $d;
                }
            } else {
                return $d;
            }

            /*
            if (isset($K[$I]) && (is_array($K[$I]) || $K[$I] instanceof \ArrayAccess)) {
                $K =&$K[$I];
            } else {
                return $d;
            }
            */

            $i = $P + 1;
        };

        return $K;

        /*
        $I = substr($k, $I);

        return isset($K[$I]) ? $K[$I] : $d;
        */
    }

    /**
     *
     */
    public function getStateFieldNotEmpty($k, $d = null)
    {
        $_result = $this->getStateField($k, $d);

        return empty($_result) ? $d : $_result;
    }

    /**
     *
     */
    public function setStateField($k, $v)
    {
        $I = 0;
        $K =&$this->_state;

        while (($P = strpos($k, '.', $I)) !== false) {
            if ($I === 0) {
                $I = $F = substr($k, $I, $P - $I);
            } else {
                $I = substr($k, $I, $P - $I);
            }

            if (isset($K[$I]) === false || (is_array($K[$I]) || $K[$I] instanceof \ArrayAccess) === false) {
                $K[$I] = [];
            }

            $K =&$K[$I];

            $I = $P + 1;
        };

        $J = substr($k, $I);

        if ($this->dirty === MicrobeModel::DRT_STABLE) {
            $this->dirty = MicrobeModel::DRT_TRACKING;
        }

        $K[$J] = $v;

        /*
        if ($I === 0) {
            $this->_stateChanged[$J] =&$K[$J];
        } else {
            $this->_stateChanged[$F] =&$K[$F];
        }
        */

        return $v;
    }

    /**
     *
     */
    public function __call(
        $alias,
        $param = null
    )
    {
        if (empty($param) === false) {
            return $this->_state[$alias] = new MicrobeObject($param[0]);
        }

        if (isset($this->_state[$alias])) {
            $_object = $this->_state[$alias];

            if ($_object instanceof MicrobeObject) {
                return $_object;
            }

            if (is_array($_object)) {
                return $this->_state[$alias] = new MicrobeObject($_object);
            }
        }

        return $this->_state[$alias] = new MicrobeObject();
    }

    /**
     *
     */
    public function run($k)
    {
        $I = 0;
        $K = $this;

        while(($P = strpos($k, '.', $I)) !== false) {
            $K = $K->__call(substr($k, $I, $P - $I));

            $I = $P + 1;
        };

        return $K;
    }
}

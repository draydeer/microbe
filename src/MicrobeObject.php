<?php

namespace Microbe;

/**
 * Class MicrobeObject
 * @package Microbe
 */
class MicrobeObject implements \ArrayAccess
{

    /**
     *
     */
    public function __construct($field = null)
    {
        if ($field) {
            foreach ($field as $k => $v) {
                $this->{$k} = $v;
            }
        }
    }

    /**
     *
     */
    public function __get($k)
    {
        return $k ? $this->{$k} = null : null;
    }

    /**
     *
     */
    public function __set($k, $v)
    {
        return $k ? $this->{$k} = $v : null;
    }

    /**
     *
     */
    public function __call($alias, $param = null)
    {
        if (empty($param)) {
            if (isset($this->{$alias})) {
                $object = $this->{$alias};

                if ($object instanceof self) {
                    return $object;
                }

                if (is_array($object)) {
                    return $this->{$alias} = new self($object);
                }
            }

            return $this->{$alias} = new self();
        }

        return $this->{$alias} = new self($param[0]);
    }

    /**
     *
     */
    public function __toString()
    {
        return json_encode($this);
    }

    /**
     *
     */
    public function&offsetGet($k)
    {
        static $null = null;

        if (isset($this->{$k})) {
            return $this->{$k};
        }

        return $null;
    }

    /**
     *
     */
    public function offsetExists($k)
    {
        return isset($this->{$k});
    }

    /**
     *
     */
    public function offsetSet($k, $v)
    {
        return $this->{$k} = $v;
    }

    /**
     *
     */
    public function offsetUnset($k)
    {
        unset($this->{$k});
    }

    /**
     *
     */
    public function setStateField($k, $v)
    {
        return $this->{$k} = $v;
    }

}

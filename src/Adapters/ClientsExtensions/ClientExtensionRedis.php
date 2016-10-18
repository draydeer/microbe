<?php

namespace Microbe\Adapters\ClientsExtensions;

use Microbe\MicrobeClientExtension;

/**
 * Class AdapterCouch
 * @package Microbe\Adapters
 */
class ClientExtensionRedis extends MicrobeClientExtension
{

    /** @var null $_key */
    protected $_key = null;

    /**
     *
     */
    public function on($k = null)
    {
        if ($k === null) {
            if ($this->_model !== null) {
                $this->_key = $this->_model->getStateFieldPK();
            } else {
                throw new \Exception();
            }
        } else {
            $this->_key = $k;
        }

        return $this;
    }

    /**
     *
     */
    public function get($field = null, $extra = null)
    {
        return $this->_client->get($this->_key);
    }

    /**
     *
     */
    public function set($value, $field = null)
    {
        return $this->_client->set($this->_key, $value);
    }

    /**
     *
     */
    public function getTTL($field = null, $extra = null)
    {
        return $this->_client->ttl($this->_key);
    }

    /**
     *
     */
    public function setTTL($value = 0, $field = null)
    {
        return $this->_client->expire($this->_key, $value);
    }
}

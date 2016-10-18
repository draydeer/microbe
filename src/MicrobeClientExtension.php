<?php

namespace Microbe;

/**
 * Class MicrobeClientExtension
 * @package Microbe
 */
abstract class MicrobeClientExtension
{

    /** @var \MongoClient|\Redis $_client */
    protected $_client;

    /** @var MicrobeModel|null $_model */
    protected $_model = null;

    /**
     *
     */
    public function __construct($client, MicrobeModel $model = null)
    {
        $this->_client = $client;

        $this->_model = $model;

        if ($this->_model !== null) {
            $this->on();
        }
    }

    /**
     *
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     *
     */
    public function on($k = null)
    {
        return $this;
    }
}

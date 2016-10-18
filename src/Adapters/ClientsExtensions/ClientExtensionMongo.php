<?php

namespace Microbe\Adapters\ClientsExtensions;

use Microbe\MicrobeClientExtension;

/**
 * Class ClientExtensionMongo
 * @package Microbe\Adapters\ClientsExtensions
 */
class ClientExtensionMongo extends MicrobeClientExtension
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
    public function getGridFS()
    {
        return $this->_client->getGridFS();
    }
}

<?php

namespace TestMocks\Connections;

use TestMocks\Adapters\Adapter;

/**
 * Class Connection
 * @package TestMocks\Adapters
 */
class Connection extends \Microbe\MicrobeConnection
{

    /**
     *
     */
    protected function onAdapterCreate()
    {
        return new Adapter($this->_microbe, $this);
    }
}

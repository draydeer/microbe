<?php

namespace Microbe\Adapters;

use Microbe\Adapters\Clients\ClientCouch;
use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;

/**
 * Class AdapterCouch
 * @package Microbe\Adapters
 */
class AdapterCouch extends MicrobeAdapter
{

    /** @var string $PK */
    protected static $PK = '_id';

    /*
     *
     */
    protected $DBRef;

    /**
     *
     */
    protected function onInit(array $connection = null)
    {
        try {
            $this->_client = new ClientCouch(
                [
                    'db'            => $connection['name']
                ]
            );

            $this->_db = $connection['name'];
        } catch (\Exception $e) {

        }
    }
}

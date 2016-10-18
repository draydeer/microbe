<?php

namespace Microbe\Traits;

use Microbe\MicrobeModelAutoCreatable;

/**
 * Class ModelAutoCreatableTrait
 * @package Microbe\Traits
 */
trait ModelAutoCreatableTrait
{

    /**
     * @param $condition
     *
     * @return null|static|\Microbe\MicrobeModel
     */
    public static function one($param = null, $alias = null)
    {
        $result = parent::one($param, $alias);

        if ($result === null) {
            $result = new static();

            $result->setFlag(MicrobeModelAutoCreatable::FLG_CREATED);
        }

        return $result;
    }

    /**
     *
     */
    public function isAutoCreated()
    {
        return $this->getFlag(MicrobeModelAutoCreatable::FLG_CREATED, true) === MicrobeModelAutoCreatable::FLG_CREATED;
    }
}

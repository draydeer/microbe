<?php

namespace Microbe;

use Microbe\Traits\ModelAutoCreatableTrait;

/**
 * Class MicrobeModelAutoCreatable
 * @package Microbe
 */
abstract class MicrobeModelAutoCreatable extends MicrobeModel
{
    use ModelAutoCreatableTrait;

    const FLG_CREATED = 2;
}

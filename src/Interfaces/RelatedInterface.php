<?php

namespace Microbe\Interfaces;

use Microbe\MicrobeState;

/**
 * Interface RelatedInterface
 * @package Microbe\Interfaces
 */
interface RelatedInterface
{

    /**
     *
     */
    public function getRelated(
        $key,
        $param = null,
        $extra = null
    );

    /**
     *
     */
    public function setRelated(
        $key,
        MicrobeState
        $value
    );
}

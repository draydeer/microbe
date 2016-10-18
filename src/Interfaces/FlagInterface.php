<?php

namespace Microbe\Interfaces;

/**
 * Interface FlagInterface
 * @package Microbe\Interfaces
 */
interface FlagInterface
{

    /**
     *
     */
    public function getFlag(
        $value,
        $forceReset = false
    );

    /**
     *
     */
    public function setFlag(
        $value,
        $forceReset = false
    );
}

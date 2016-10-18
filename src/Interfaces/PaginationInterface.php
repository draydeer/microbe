<?php

namespace Microbe\Interfaces;

/**
 * Interface PaginationInterface
 * @package Microbe\Interfaces
 */
interface PaginationInterface
{

    /**
     *
     */
    public function paginate(
        $index = 0,
        $limit = 15,
        $forceAggCount = true
    );
}

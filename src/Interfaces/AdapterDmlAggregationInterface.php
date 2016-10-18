<?php

namespace Microbe\Interfaces;

use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;

/**
 * Interface AdapterDmlAggregationInterface
 * @package Microbe\Interfaces
 */
interface AdapterDmlAggregationInterface
{

    /**
     * Op. aggregation of average.
     */
    public function _aggAverage(
        MicrobeModelMetadata $metadata,
        $condition = null,
        $extra = null,
        $fetch = null,
        $forceNotThrow = false
    );

    /**
     * Op. aggregation of count.
     */
    public function _aggCount(
        MicrobeModelMetadata $metadata,
        $condition = null,
        $extra = null,
        $fetch = null,
        $forceNotThrow = false
    );

    /**
     * Op. aggregation of max value.
     */
    public function _aggMax(
        MicrobeModelMetadata $metadata,
        $condition = null,
        $extra = null,
        $fetch = null,
        $forceNotThrow = false
    );

    /**
     * Op. aggregation of min value.
     */
    public function _aggMin(
        MicrobeModelMetadata $metadata,
        $condition = null,
        $extra = null,
        $fetch = null,
        $forceNotThrow = false
    );

    /**
     * Op. aggregation of sum of values.
     */
    public function _aggSum(
        MicrobeModelMetadata $metadata,
        $condition = null,
        $extra = null,
        $fetch = null,
        $forceNotThrow = false
    );
}

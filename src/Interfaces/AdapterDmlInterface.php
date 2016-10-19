<?php

namespace Microbe\Interfaces;

use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;

/**
 * Interface AdapterDmlInterface
 * @package Microbe\Interfaces
 */
interface AdapterDmlInterface
{

    /**
     * Op. delete general.
     */
    public function _del(
        MicrobeModelMetadata $model,
        $condition = null,
        $extra = null,
        $watchType = MicrobeAdapter::TYP_WATCH_ORIGIN,
        $forceThrow = false
    );

    /**
     * Op. insert general.
     */
    public function _ins(
        MicrobeModelMetadata $model,
        $value,
        $extra = null,
        $watchType = MicrobeAdapter::TYP_WATCH_ORIGIN,
        $forceThrow = false
    );

    /**
     * Op. insert bulk (plenty of values at once).
     */
    public function _insBulk(
        MicrobeModelMetadata $model,
        $value,
        $extra = null,
        $watchType = MicrobeAdapter::TYP_WATCH_ORIGIN,
        $forceThrow = false
    );

    /**
     * Op. select general.
     */
    public function _sel(
        MicrobeModelMetadata $model,
        $condition = null,
        $extra = null,
        $forceFetch = false,
        $forceThrow = false
    );

    /**
     * Op. select chunk (limit, offset).
     */
    public function _selChunk(
        MicrobeModelMetadata $model,
        $condition = null,
        $limit = 1,
        $offset = 0,
        $forceThrow = false
    );

    /**
     * Op. select in (list of referenced fields).
     */
    public function _selIn(
        MicrobeModelMetadata $model,
        $in,
        $pk,
        $forceFetch = false,
        $forceThrow = false
    );

    /**
     * Op. select by PK.
     */
    public function _selPK(
        MicrobeModelMetadata $model,
        $pkValue,
        $pk = null,
        $forceFetch = false,
        $forceThrow = false
    );

    /**
     * Op. update general.
     */
    public function _upd(
        MicrobeModelMetadata $model,
        $value,
        $condition = null,
        $watchType = MicrobeAdapter::TYP_WATCH_ORIGIN,
        $forceThrow = false
    );

    /**
     * Op. update partial.
     */
    public function _updPartial(
        MicrobeModelMetadata $model,
        $value,
        $condition = null,
        $watchType = MicrobeAdapter::TYP_WATCH_ORIGIN,
        $forceThrow = false
    );

    /**
     * Op. update partial by PK value.
     */
    public function _updPartialPK(
        MicrobeModelMetadata $model,
        $value,
        $condition = null,
        $watchType = MicrobeAdapter::TYP_WATCH_ORIGIN,
        $forceThrow = false
    );

    /**
     * Op. update by PK value.
     */
    public function _updPK(
        MicrobeModelMetadata $model,
        $value,
        $condition = null,
        $watchType = MicrobeAdapter::TYP_WATCH_ORIGIN,
        $forceThrow = false
    );
}

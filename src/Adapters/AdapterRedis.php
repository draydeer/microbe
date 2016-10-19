<?php

namespace Microbe\Adapters;

use Microbe\Adapters\ClientsExtensions\ClientExtensionRedis;
use Microbe\Exceptions\Adapter\AdapterNativeClientNotImplementedException;
use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;
use Microbe\MicrobeModel;

/**
 * Class AdapterRedis
 * @package Microbe\Adapters
 */
class AdapterRedis extends MicrobeAdapter
{

    const TYP_WATCH_SIMPLE = 2;

    const TYP_WATCH_COMPLEX = 3;

    /** @var string $PK */
    protected static $PK = 'id';

    /**
     *
     */
    protected function onGetClientExtension()
    {
        return new ClientExtensionRedis($this->_client);
    }

    /**
     *
     */
    protected function onGetClientExtensionForModel(MicrobeModel $model)
    {
        return new ClientExtensionRedis($this->_client, $model);
    }

    /**
     *
     */
    protected function onInit(array $connection = null)
    {
        try {
            if (class_exists('\\Redis') === false) {
                throw new AdapterNativeClientNotImplementedException('Requires: Redis');
            }

            $this->_client = new \Redis();

            $this->_client->connect($connection['host']);

            $this->_db = $connection['name'];
            $this->_dbTarget = null;
        } catch (\Exception $e) {

        }
    }

    /**
     *
     */
    public function _sel(
        MicrobeModelMetadata $model,
        $param,
        $extra = null,
        $fetch = null
    )
    {
        if (isset($param[$model->pk])) {
            $result = $this->_client->hGetAll($param[$model->pk]);

            if (empty($result) === false) {
                $result[$model->pk] = $param[$model->pk];

                return [$result];
            }
        }

        return [];
    }

    /**
     *
     */
    public function _selChunk(
        MicrobeModelMetadata $model,
        $param,
        $limit = 1,
        $offset = 0
    )
    {
        if (isset($param[$model->pk])) {
            $_result = $this->_client->hGetAll($param[$model->pk]);

            if (empty($_result) === false) {
                $_result[$model->pk] = $param[$model->pk];

                return [ $_result ];
            }
        }

        return [];
    }

    /**
     *
     */
    public function _del(
        MicrobeModelMetadata $model,
        $param = null,
        $extra = null,
        $watchType = self::TYP_WATCH_COMPLEX
    )
    {
        if (isset($param[$model->pk])) {
            return $this->_client->del($param[$model->pk]);
        }

        return true;
    }

    /**
     *
     */
    public function _ins(
        MicrobeModelMetadata $model,
        $value,
        $extra = null,
        $watchType = self::TYP_WATCH_COMPLEX
    )
    {
        if (isset($value[$model->pk])) {
            foreach ($value as $I => $V) {
                if ($I !== $model->pk) {
                    $this->_client->hSet($value[$model->pk], $I, $V);
                }
            }

            return $value[$model->pk];
        }

        return false;
    }

    /**
     *
     */
    public function _upd(
        MicrobeModelMetadata $model,
        $value,
        $param = null,
        $watchType = self::TYP_WATCH_COMPLEX
    )
    {

    }

    /**
     *
     */
    public function _updPK(
        MicrobeModelMetadata $model,
        $value,
        $param = null,
        $watchType = self::TYP_WATCH_COMPLEX
    )
    {
        if (isset($value[$model->pk])) {
            if ($this->_client->del($value[$model->pk])) {
                return $this->_ins($value[$model->pk], $value);
            }
        }

        return false;
    }
}

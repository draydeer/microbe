<?php

namespace Microbe\Adapters;

use Microbe\Adapters\ClientsExtensions\ClientExtensionMongo;
use Microbe\Adapters\Criterias\CriteriaMongo;
use Microbe\Adapters\Cursors\CursorMongo;
use Microbe\Adapters\ExceptionClasses\Mongo;
use Microbe\Exceptions\Adapter\AdapterIncompatibleConditionException;
use Microbe\Exceptions\Adapter\AdapterNativeClientNotImplementedException;
use Microbe\Exceptions\ConnectionException;
use Microbe\Exceptions\NotImplementedException;
use Microbe\Exceptions\RequestException;
use Microbe\Microbe;
use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;
use Microbe\MicrobeModel;

/**
 * Class AdapterMongo
 * @package Microbe\Adapters
 */
class AdapterMongo extends MicrobeAdapter
{

    /** @var string $PK */
    protected static $PK = '_id';

    /** @var int $_curBatchSize */
    public $_curBatchSize = 1024;

    /** @var int $_curTimeout */
    public $_curTimeout = 600;

    /**
     *
     */
    protected function prepareDataWithoutPk(MicrobeModelMetadata $metadata, $value)
    {
        unset($value[$metadata->pk]);

        return $value;
    }

    /**
     *
     */
    protected function prepareExtra($cursor, $extra)
    {
        if (isset($extra['limit'])) {
            $cursor->limit($extra['limit']);
        }

        if (isset($extra['offset'])) {
            $cursor->skip($extra['offset']);
        }

        if (isset($extra['sort'])) {
            $cursor->sort($extra['sort']);
        }
    }

    /**
     *
     */
    protected function onGetClientExtension()
    {
        return new ClientExtensionMongo($this->_client);
    }

    /**
     *
     */
    protected function onGetClientExtensionForModel(MicrobeModel $model)
    {
        return new ClientExtensionMongo($this->_client, $model);
    }

    /**
     *
     */
    protected function onInitConnection()
    {
        $this->_connected = false;

        while (1) {
            try {
                if (class_exists('\\MongoClient') === false) {
                    throw new AdapterNativeClientNotImplementedException('Requires Mongo');
                }

                $param = [
                    'db' => $this->_connection['name']
                ];

                if (isset($this->_connection['options']['batchSize'])) {
                    $this->_curBatchSize = $this->_connection['options']['batchSize'];
                }

                if (isset($this->_connection['options']['connectTimeout'])) {
                    $param['connectTimeoutMS'] = $this->_connection['options']['connectTimeout'] * 1000;
                }

                if (isset($this->_connection['options']['timeout'])) {
                    $this->_curTimeout = ($param['socketTimeoutMS'] = $this->_connection['options']['timeout'] * 1000) / 1000;
                }

                if (isset($this->_connection['options']['w'])) {
                    $param['w'] = $this->_connection['options']['w'];
                }

                if (isset($this->_connection['options']['wTimeout'])) {
                    $param['wTimeoutMS'] = $this->_curTimeout = $this->_connection['options']['wTimeout'] * 1000;
                }

                if (isset($this->_connection['user'])) {
                    $param['username'] = $this->_connection['user'];
                }

                if (isset($this->_connection['pass'])) {
                    $param['password'] = $this->_connection['pass'];
                }

                $this->_client = new \MongoClient(
                    'mongodb://' . $this->_connection['host'],
                    $param
                );

                $this->_db = $this->_connection['name'];
                $this->_dbTarget = $this->_client->{ $this->_db };

                return $this;
            } catch (\Exception $e) {
                if ($this->processExceptionOnConnection($e) === false) {
                    throw ConnectionException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public static function getCondition($model, $param = null)
    {
        if ($model instanceof CriteriaMongo) {
            return $model->getCondition();
        }

        if ($model instanceof MicrobeModelMetadata) {
            return $param;
        }

        throw new AdapterIncompatibleConditionException('On: ' . static::whoAmI());
    }

    /**
     *
     */
    public static function getCriteria($alias)
    {
        return new CriteriaMongo($alias);
    }

    /**
     *
     */
    public static function getCursor(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        callable $f = null
    )
    {
        return $f ? new CursorMongo($metadata, $param, $extra, $f) : new CursorMongo($metadata, $param, $extra);
    }

    /**
     *
     */
    public static function getFetchable($cursor, $timeout = 0)
    {
        return CursorMongo::getFetchable($cursor, $timeout = 0);
    }

    /**
     *
     */
    public static function getPKValue($value)
    {
        return new \MongoId($value);
    }

    /**
     *
     */
    public function getClientTargetSource(MicrobeModelMetadata $metadata)
    {
        return $this->onInitConnection()->_dbTarget->{ $metadata->schema };
    }

    /**
     *
     */
    public function getType()
    {
        return Microbe::MONGO;
    }

    /**
     *
     */
    public function examine(\Exception $e)
    {
        return Mongo::examine($e);
    }

    /**
     *
     */
    public function _aggCount(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        $fetch = null,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        $param = static::getCondition($metadata, $param);

        while (1) {
            try {

                /** @var \MongoCursor $cursor */
                $cursor = $this->_dbTarget->{$metadata->schema}->find($param)->batchSize($this->_curBatchSize);

                if ($extra !== null) {
                    $this->prepareExtra($cursor, $extra);
                }

                return $cursor->count();
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _aggMax(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        $fetch = null,
        $forceThrow = false
    )
    {

    }

    /**
     *
     */
    public function _aggAverage(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        $fetch = null,
        $forceThrow = false
    )
    {

    }

    /**
     *
     */
    public function _aggMin(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        $fetch = null,
        $forceThrow = false
    )
    {

    }

    /**
     *
     */
    public function _aggSum(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        $fetch = null,
        $forceThrow = false
    )
    {

    }

    /**
     *
     */
    public function _del(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {

                /** @var \MongoCursor $result */
                $result = $this->_dbTarget->{$metadata->schema}->remove($param);

                return $result;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _ins(
        MicrobeModelMetadata $metadata,
        $value,
        $extra = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {

                /** @var \MongoCursor $result */
                $result = $this->_dbTarget->{$metadata->schema}->insert($value);

                return $result ? $value[$metadata->pk] : $result;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _insBulk(
        MicrobeModelMetadata $metadata,
        $value,
        $extra = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {

                /** @var \MongoCursor $result */
                $result = $this->_dbTarget->{$metadata->schema}->batchInsert($value);

                return $result;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _sel(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        $forceFetch = true,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        $param = static::getCondition($metadata, $param);

        while (1) {
            try {

                /** @var \MongoCursor $cursor */
                $cursor = $this->_dbTarget->{ $metadata->schema }->find($param ? $param : [], $this->getParam($extra, 'columns', []))->batchSize($this->_curBatchSize);

                if ($extra !== null) {
                    $this->prepareExtra($cursor, $extra);
                }

                return $forceFetch ? static::getFetchable($cursor, $this->_curTimeout) : $cursor;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _selChunk(
        MicrobeModelMetadata $metadata,
        $param = null,
        $limit = 1,
        $offset = 0,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        $param = static::getCondition($metadata, $param);

        while (1) {
            try {

                /** @var \MongoCursor $cursor */
                $cursor = $this->_dbTarget->{$metadata->schema}->find($param ? $param : [])->batchSize($this->_curBatchSize);

                $this->prepareExtra($cursor, [
                    'limit' => $limit, 'offset' => $offset,
                ]);

                return static::getFetchable($cursor, $this->_curTimeout);
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _selIn(
        MicrobeModelMetadata $metadata,
        $in,
        $pk,
        $forceFetch = true,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        $param = [$pk => ['$in' => $in]];

        while (1) {
            try {

                /** @var \MongoCursor $cursor */
                $cursor = $this->_dbTarget->{$metadata->schema}->find($param ? $param : [])->batchSize($this->_curBatchSize);

                $this->prepareExtra($cursor, [

                ]);

                return $forceFetch ? static::getFetchable($cursor, $this->_curTimeout) : $cursor;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _selPK(
        MicrobeModelMetadata $metadata,
        $value,
        $pk = null,
        $forceFetch = true,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        $param = [$metadata->pk => $this->getPKValue($value)];

        while (1) {
            try {

                /** @var \MongoCursor $cursor */
                $cursor = $this->_dbTarget->{ $metadata->schema }->find($param ? $param : [])->batchSize($this->_curBatchSize);

                $this->prepareExtra($cursor, [

                ]);

                return $forceFetch ? static::getFetchable($cursor, $this->_curTimeout) : $cursor;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _upd(
        MicrobeModelMetadata $metadata,
        $value,
        $param = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {

                /** @var \MongoCursor $result */
                $result = $this->_dbTarget->{$metadata->schema}->update(
                    $param,
                    $value
                );

                return $result;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _updPartial(
        MicrobeModelMetadata $metadata,
        $value,
        $param = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {

                /** @var \MongoCursor $result */
                $result = $this->_dbTarget->{$metadata->schema}->update(
                    $param,
                    ['$set' => $this->prepareDataWithoutPk($metadata, $value)]
                );

                return $result;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _updPartialPK(
        MicrobeModelMetadata $metadata,
        $value,
        $param = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {

                /** @var \MongoCursor $result */
                $result = $this->_dbTarget->{$metadata->schema}->update(
                    [$metadata->pk => $value[$metadata->pk]],
                    ['$set' => $this->prepareDataWithoutPk($metadata, $value)]
                );

                return $result;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _updPK(
        MicrobeModelMetadata $metadata,
        $value,
        $param = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {

                /** @var \MongoCursor $result */
                $result = $this->_dbTarget->{$metadata->schema}->update([$metadata->pk => $value[$metadata->pk]], $value);

                return $result;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

}

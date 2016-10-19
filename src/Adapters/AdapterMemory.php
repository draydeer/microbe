<?php

namespace Microbe\Adapters;

use Microbe\Adapters\ClientsExtensions\ClientExtensionMongo;
use Microbe\Adapters\Criterias\CriteriaMongo;
use Microbe\Adapters\Cursors\CursorMemory;
use Microbe\Exceptions\Adapter\AdapterIncompatibleConditionException;
use Microbe\Exceptions\RequestException;
use Microbe\Microbe;
use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;
use Microbe\MicrobeModel;

/**
 * Class AdapterMemory
 * @package Microbe\Adapters
 */
class AdapterMemory extends MicrobeAdapter
{

    /** @var string $PK */
    protected static $PK = '_id';

    /** @var array $_container */
    public $_container = [];

    /** @var int $_containerAutoIncrement */
    protected $_containerAutoIncrement = 1;

    /**
     *
     */
    protected function doRequest(
        MicrobeModelMetadata $metadata,
        $query,
        $param = null,
        $extra = null
    )
    {
        $index = 0;

        $limit = $this->getParam($extra, 'limit', - 1);

        $offset = $this->getParam($extra, 'offset', 0);

        foreach ($this->_container as $k => $v) {
            if ($index ++ < $offset) {
                continue;
            }

            $fetch = true;

            foreach ($param as $field => $condition) {
                if (isset($v[$field])) {
                    if (is_array($condition)) {
                        switch ($condition[0]) {
                            case '$exists':
                                $fetch &= true;

                                break;

                            case '$gt':
                                $fetch &= $v[$field] > $condition[1];

                                break;

                            case '$gte':
                                $fetch &= $v[$field] >= $condition[1];

                                break;

                            case '$in':
                                $fetch &= in_array($v[$field], is_array($condition[1]) ? $condition[1] : [$condition[1]], true);

                                break;

                            case '$lt':
                                $fetch &= $v[$field] < $condition[1];

                                break;

                            case '$lte':
                                $fetch &= $v[$field] <= $condition[1];

                                break;

                            case '$ne':
                                $fetch &= $v[$field] !== $condition[1];

                                break;

                            case '$regex':
                                $fetch &= filter_var($v[$field], FILTER_VALIDATE_REGEXP, $condition[1]) !== false;

                                break;

                            default:
                                $fetch = false;
                        }
                    } else {
                        $fetch = $v[$field] === $condition;
                    }
                } else {
                    $fetch = false;
                }

                if ($fetch === false) {
                    break;
                }
            }

            if ($fetch) {
                yield $k => $v;

                if (-- $limit === 0) {
                    break;
                }
            }
        }
    }

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
        $this->_connected = true;
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
        return $f ? new CursorMemory($metadata, $param, $extra, $f) : new CursorMemory($metadata, $param, $extra);
    }

    /**
     *
     */
    public static function getFetchable($cursor, $timeout = 0)
    {
        return CursorMemory::getFetchable($cursor, $timeout);
    }

    /**
     *
     */
    public static function getPKValue($value)
    {
        return $value;
    }

    /**
     *
     */
    public function getClientTargetSource(MicrobeModelMetadata $metadata)
    {
        return $this->_container;
    }

    /**
     *
     */
    public function getType()
    {
        return Microbe::MEMORY;
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
                foreach ($this->doRequest($metadata, null, $param, $extra) as $k => $v) {
                    unset($this->_container[$k]);
                }

                return true;
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
                $value[$metadata->pk] = $this->_containerAutoIncrement ++;

                $this->_container[$value[$metadata->pk]] = $value;

                return $value[$metadata->pk];
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
        foreach ($value as $v) {
            $this->_ins($metadata, $v, $extra, $watchType, $forceThrow);
        }

        return true;
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

        while (1) {
            try {

                /** @var mixed $cursor */
                $cursor = $this->doRequest($metadata, null, $param, $extra);

                return $forceFetch ? static::getFetchable($cursor) : $cursor;
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

        while (1) {
            try {

                /** @var mixed $cursor */
                $cursor = $this->doRequest($metadata, null, $param, ['limit' => $limit, 'offset' => $offset]);

                return static::getFetchable($cursor);
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

        while (1) {
            try {

                /** @var mixed $cursor */
                $cursor = $this->doRequest($metadata, null, ['$in' => $in]);

                return $forceFetch ? static::getFetchable($cursor) : $cursor;
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

        while (1) {
            try {

                /** @var mixed $cursor */
                $cursor = $this->doRequest($metadata, null, [$metadata->pk => $this->getPKValue($value)]);

                return $forceFetch ? static::getFetchable($cursor) : $cursor;
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
                foreach ($this->doRequest($metadata, null, $param) as $k => $v) {
                    $this->_container[$k] = $v;
                }

                return true;
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
                foreach ($this->doRequest($metadata, null, $param) as $k => $v) {
                    $this->_container[$k] = array_merge($this->_container[$k], $value);
                }

                return true;
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
                foreach ($this->doRequest($metadata, null, [$metadata->pk => $value[$metadata->pk]]) as $k => $v) {
                    $this->_container[$k] = array_merge($this->_container[$k], $value);
                }

                return true;
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
                foreach ($this->doRequest($metadata, null, [$metadata->pk = $value[$metadata->pk]]) as $k => $v) {
                    $this->_container[$k] = $v;
                }

                return true;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

}

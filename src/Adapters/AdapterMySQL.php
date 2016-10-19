<?php

namespace Microbe\Adapters;

use Microbe\Adapters\Criterias\CriteriaMySQL;
use Microbe\Adapters\Cursors\CursorMySQL;
use Microbe\Adapters\Dialects\Traits\TraitDialectMySQL;
use Microbe\Adapters\ExceptionClasses\Mysql;
use Microbe\Exceptions\Adapter\AdapterIncompatibleConditionException;
use Microbe\Exceptions\Adapter\AdapterNativeClientNotImplementedException;
use Microbe\Exceptions\ConnectionException;
use Microbe\Exceptions\Exception;
use Microbe\Exceptions\NotImplementedException;
use Microbe\Exceptions\OperationException;
use Microbe\Exceptions\RequestException;
use Microbe\Microbe;
use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;

/**
 * Class AdapterMySQL
 * @package Microbe\Adapters
 */
class AdapterMySQL extends MicrobeAdapter
{
    use TraitDialectMySQL;

    /** @var string $PK */
    protected static $PK = 'id';

    /** @var \PDO $_client */
    protected $_client;

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
        if ($extra !== null) {
            if (isset($extra['limit'])) {
                $extra = ' ' . static::getDialectForLIM($extra['limit'], static::getParam($extra, 'offset', 0));
            } elseif (isset($extra['offset'])) {
                $extra = ' ' . static::getDialectForLIM(null, $extra['offset']);
            }
        }

        if (is_array($param)) {
            $cursor = $this->_client->prepare($query . ' WHERE ' . $param[0] . $extra);

            $cursor->execute($param[1]);
        } else {
            $cursor = $this->_client->prepare($query . $extra);

            $cursor->execute();
        }

        return $cursor;
    }

    /**
     *
     */
    protected function onInitConnection()
    {
        $this->_connected = false;

        while (1) {
            try {
                if (class_exists('\\PDO') === false) {
                    throw new AdapterNativeClientNotImplementedException('Requires PDO');
                }

                $this->_client = new \PDO(
                    'mysql:dbname=' . $this->_connection['name'] . ';host=' . $this->_connection['host'],
                    $this->_connection['user'],
                    $this->_connection['pass'],
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_PERSISTENT => $this->getParam(
                            $this->_connection['options'],
                            'persistent',
                            true
                        ),
                        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->getParam(
                            $this->_connection['options'],
                            'encoding',
                            'utf8'
                        ),
                    ]
                );

                $this->_db = $this->_connection['name'];
                $this->_dbTarget = null;

                return $this;
            } catch (\Exception $e) {
                if ($this->processExceptionOnConnection($e) === false) {
                    throw ConnectionException::create($e->getMessage(), $e->getCode(), $e);
                }
            }
        }
    }

    /**
     *
     */
    public static function getCondition($model, $param = null)
    {
        if ($model instanceof CriteriaMySQL) {
            return $model->getCondition();
        }

        if ($model instanceof MicrobeModelMetadata) {
            if (is_array($param) === false || count($param) === 0) {
                return '1';
            }

            $result = CriteriaMySQL::compileParametrized(
                $model->schema,
                $param,
                false,
                $model->pk
            );

            return $result;
        }

        throw new AdapterIncompatibleConditionException('On: ' . static::whoAmI());
    }

    /**
     *
     */
    public static function getCriteria($alias)
    {
        return new CriteriaMySQL($alias);
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
        return $f ? new CursorMySQL($metadata, $param, $extra, $f) : new CursorMySQL($metadata, $param, $extra);
    }

    /**
     *
     */
    public static function getFetchable($cursor, $timeout = 0)
    {
        return CursorMySQL::getFetchable($cursor, $timeout = 0);
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
    public function getType()
    {
        return Microbe::MYSQL;
    }

    /**
     *
     */
    public function examine(\Exception $e)
    {
        return Mysql::examine($e);
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

        foreach (CursorMySQL::getFetchable(
            $this->doRequest(
                $metadata,
                'SELECT COUNT(1) AS ' . $metadata->alias . '_c FROM ' . $metadata->schema,
                static::getCondition($metadata, $param),
                $extra
            )
        ) as $state) {
            return $state[$metadata->alias . '_c'];
        }
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
                $_result = $this->doRequest(
                    $metadata,
                    'DELETE FROM ' . $metadata->schema,
                    static::getCondition($metadata, $param),
                    $extra
                );

                return $_result;
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

        $query = 'INSERT INTO ' . $metadata->schema . ' (';
        $queryValues = ') VALUES (';

        foreach ($value as $k => $v) {
            $query.= $k . ',';
            $queryValues.= ':' . $k . ',';
        }

        while (1) {
            try {
                $_result = $this->_client->prepare(trim($query, ',') . trim($queryValues, ',') . ')')->execute($value);

                if ($_result) {
                    return $this->_client->lastInsertId(static::getDialectForLastInsertID($metadata->schema . '_' . $metadata->pk));
                }

                throw new OperationException('See connection->getLastError()');
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

        $query = 'INSERT INTO ' . $metadata->schema . ' (';
        $queryValues = '(';

        foreach ($value[0] as $k => $v) {
            $query.= $k . ',';
            $queryValues.= '?,';
        }

        $queryValues = ') VALUES ' . str_repeat(trim($queryValues, ',') . '),', count($value));
        $template = [];

        foreach ($value as $v) foreach ($v as $v) {
            array_push($template, $v);
        }

        while (1) {
            try {
                return $this->_client->prepare(trim($query, ',') . trim($queryValues, ','))->execute($template);
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

        while (1) {
            try {
                $cursor = $this->doRequest(
                    $metadata,
                    'SELECT ' . $metadata->schema . '.* FROM ' . $metadata->schema,
                    static::getCondition($metadata, $param),
                    $extra
                );

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
                $cursor = $this->doRequest(
                    $metadata,
                    'SELECT ' . $metadata->schema . '.* FROM ' . $metadata->schema,
                    static::getCondition($metadata, $param),
                    ['limit' => $limit, 'offset' => $offset]
                );

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
        $field,
        $forceFetch = true,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {
                $cursor = $this->doRequest(
                    $metadata,
                    'SELECT ' . $metadata->schema . '.* FROM ' . $metadata->schema,
                    [$field . ' IN (' . trim(str_repeat('?,', count($in)), ',') . ')', $in],
                    null
                );

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
                $cursor = $this->doRequest(
                    $metadata,
                    'SELECT ' . $metadata->schema . '.* FROM ' . $metadata->schema,
                    [$metadata->pk . '=:' . $metadata->pk, [$metadata->pk => $this->getPKValue($value)]],
                    null
                );

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
                $result = $this->doRequest(
                    $metadata,
                    'UPDATE ' . $metadata->schema . ' SET ' . static::getBindingKeyValueFromKeys($value),
                    [$metadata->pk . '=:' . $metadata->pk, $value],
                    null
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
                $result = $this->doRequest(
                    $metadata,
                    'UPDATE ' . $metadata->schema . ' SET ' . static::getBindingKeyValueFromKeys($value),
                    [$metadata->pk . '=:' . $metadata->pk, $value],
                    null
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
                $result = $this->doRequest(
                    $metadata,
                    'UPDATE ' . $metadata->schema . ' SET ' . static::getBindingKeyValueFromKeys($value),
                    [$metadata->pk . '=:' . $metadata->pk, $value],
                    null
                );

                return $result;
            } catch (\Exception $e) {
                if ($this->processExceptionOnRequest($e) === false) {
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
                $result = $this->doRequest(
                    $metadata,
                    'UPDATE ' . $metadata->schema . ' SET ' . static::getBindingKeyValueFromKeys($value),
                    [$metadata->pk . '=:' . $metadata->pk, $value],
                    null
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
    public function getLastError()
    {
        return $this->_client->errorInfo();
    }

}

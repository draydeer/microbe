<?php

namespace Microbe;

use Microbe\Exceptions\NotImplementedException;
use Microbe\Interfaces\AdapterDmlAggregationInterface;
use Microbe\Interfaces\AdapterDmlInterface;
use Microbe\Traits\Ext\GetInstanceTrait;

/**
 * Class MicrobeAdapter
 * @package Microbe
 */
abstract class MicrobeAdapter extends MicrobeBase implements AdapterDmlInterface, AdapterDmlAggregationInterface
{
    use GetInstanceTrait;

    const TYP_WATCH_ORIGIN = 0;

    /** @var string $PK */
    protected static $PK;

    /** @var \MongoClient|\PDO|\Redis $_client */
    protected $_client;

    /** @var mixed $_clientExt */
    protected $_clientExt = null;

    /** @var bool $_connected */
    protected $_connected = false;

    /** @var int $_curTimeout */
    protected $_curTimeout = 600;

    /** @var string $_db */
    protected $_db;

    /** @var mixed $_dbTarget */
    protected $_dbTarget;

    /** @var int $_exceptionHandlingPause */
    protected $_exceptionHandlingPause = 1;

    /** @var MicrobeConnection $_connection */
    protected $_connection;

    /** @var callable|null $_exceptionHandlerOnConnection */
    protected $_exceptionHandlerOnConnection;

    /** @var callable|null $_exceptionHandlerOnCursorFetch */
    protected $_exceptionHandlerOnCursorFetch;

    /** @var callable|null $_exceptionHandlerOnRequest */
    protected $_exceptionHandlerOnRequest;

    /**
     * @return MicrobeClientExtension
     */
    protected function onGetClientExtension()
    {
        throw new NotImplementedException('On client extension for: ' . static::whoAmI());
    }

    /**
     * @return MicrobeClientExtension
     */
    protected function onGetClientExtensionForModel(MicrobeModel $model)
    {
        throw new NotImplementedException('On client extension for: ' . static::whoAmI());
    }

    /**
     *
     */
    protected function onInitConnection()
    {
        $this->_connected = false;

        return $this;
    }

    /**
     *
     */
    public static function getCondition($model, $param = null)
    {
        throw new NotImplementedException('On condition for: ' . static::whoAmI());
    }

    /**
     *
     */
    public static function getCriteria($alias)
    {
        throw new NotImplementedException('On criteria for: ' . static::whoAmI());
    }

    /**
     * @return MicrobeCursor
     */
    public static function getCursor(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        callable $f = null
    )
    {
        throw new NotImplementedException('On cursor for: ' . static::whoAmI());
    }

    /**
     *
     */
    public static function getFetchable($cursor, $timeout = 0)
    {
        throw new NotImplementedException('On cursor for: ' . static::whoAmI());
    }

    /**
     *
     */
    public static function getSchema($table, $tableSchema = null)
    {
        return $table;
    }

    /**
     *
     */
    public static function getPK()
    {
        return static::$PK;
    }

    /**
     *
     */
    public static function getPKValue($value)
    {
        return $value;
    }

    /**
     * @param Microbe $microbe Microbe instance.
     * @param MicrobeConnection $connection Microbe connection instance.
     */
    public function __construct(Microbe $microbe, MicrobeConnection $connection)
    {
        $this->_connection = $connection;
        $this->_microbe = $microbe;
    }

    /**
     * @return \MongoClient|\PDO|\Redis
     */
    public function getClient()
    {
        if (empty($this->_client)) {
            $this->onInitConnection();
        }

        return $this->_client;
    }

    /**
     * @return null|\MongoCollection
     */
    public function getClientTargetSource(MicrobeModelMetadata $metadata)
    {
        throw new NotImplementedException('On target source: ' . static::whoAmI());
    }

    /**
     * @return MicrobeClientExtension
     */
    public function getClientExtension(MicrobeModel $model = null)
    {
        return $model
            ? ($this->_clientExt === null
                ? $this->_clientExt = static::onGetClientExtension()
                : $this->_clientExt)
            : static::onGetClientExtensionForModel($model);
    }

    /**
     * @return MicrobeConnection
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @return int
     */
    public function getCursorTimeout()
    {
        return $this->_curTimeout;
    }

    /**
     *
     */
    public function getLastError()
    {
        return null;
    }

    /**
     *
     */
    abstract public function getType();

    /**
     *
     */
    public function setExceptionHandlerOnConnection(callable $f)
    {
        $this->_exceptionHandlerOnConnection = $f;

        return $this;
    }

    /**
     *
     */
    public function setExceptionHandlerOnCursorFetch(callable $f)
    {
        $this->_exceptionHandlerOnCursorFetch = $f;

        return $this;
    }

    /**
     *
     */
    public function setExceptionHandlerOnRequest(callable $f)
    {
        $this->_exceptionHandlerOnRequest = $f;

        return $this;
    }

    /**
     *
     */
    public function setExceptionHandlingPause($value = null)
    {
        $this->_exceptionHandlingPause = $value > 0 ? $value + 0 : 0;

        return $this;
    }

    /**
     *
     */
    public function connectionRefresh()
    {
        return $this->onInitConnection();
    }

    /**
     *
     */
    public function examine(\Exception $e)
    {
        return null;
    }

    /**
     *
     */
    public function processExceptionOnConnection(\Exception $e)
    {
        sleep($this->_exceptionHandlingPause);

        if ($this->_exceptionHandlerOnConnection) {
            $method = $this->_exceptionHandlerOnConnection;

            return $method($e, $this->examine($e), $this);
        }

        if ($this->_microbe->getExceptionHandlerOnConnection()) {
            $method = $this->_microbe->getExceptionHandlerOnConnection();

            return $method($e, $this->examine($e), $this);
        }

        return false;
    }

    /**
     *
     */
    public function processExceptionOnCursorFetch(\Exception $e)
    {
        sleep($this->_exceptionHandlingPause);

        if ($this->_exceptionHandlerOnCursorFetch) {
            $method = $this->_exceptionHandlerOnCursorFetch;

            return $method($e, $this->examine($e), $this);
        }

        if ($this->_microbe->getExceptionHandlerOnCursorFetch()) {
            $method = $this->_microbe->getExceptionHandlerOnCursorFetch();

            return $method($e, $this->examine($e), $this);
        }

        return false;
    }

    /**
     *
     */
    public function processExceptionOnRequest(\Exception $e)
    {
        sleep($this->_exceptionHandlingPause);

        if ($this->_exceptionHandlerOnRequest) {
            $method = $this->_exceptionHandlerOnRequest;

            return $method($e, $this->examine($e), $this);
        }

        if ($this->_microbe->getExceptionHandlerOnRequest()) {
            $method = $this->_microbe->getExceptionHandlerOnRequest();

            return $method($e, $this->examine($e), $this);
        }

        return false;
    }

    /**
     *
     */
    public function refreshConnection()
    {
        $this->onInitConnection();

        return $this;
    }

}

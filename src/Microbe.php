<?php

namespace Microbe;

use Microbe\Exceptions\Connection\ConnectionNotExistsException;
use Microbe\Exceptions\NotImplementedException;
use Microbe\Services\ServiceValidator;
use Microbe\Traits\Ext\GetInstanceTrait;

/**
 * Class Microbe - data mapper
 * @package Microbe
 */
class Microbe
{
    use GetInstanceTrait;

    const

        // https://github.com/datastax/php-driver
        CASSANDRA = 'Cassandra',

        // internal driver
        COUCH = 'Couch',

        // internal driver
        ELASTIC_SEARCH = 'ElasticSearch',

        //
        DB2 = 'DB2',

        //
        MEMCACHED = 'Memcached',

        //
        MEMORY = 'Memory',

        // \Mongo
        MONGO = 'Mongo',

        // \PDO
        MSSQL = 'MSSQL',

        // \PDO
        MYSQL = 'MySQL',

        // \PDO
        ORACLE = 'Oracle',

        // \PDO pgsql
        POSTGRESQL = 'PostgreSQL',

        // \Redis
        REDIS = 'Redis',

        // \PDO
        SQLITE = 'SQLite';

    const

        // has one
        ONE = MicrobeRelation::ONE,

        // has many
        ALL = MicrobeRelation::ALL,

        // belongs to
        OWN = MicrobeRelation::OWN,

        // mapped
        THROUGH = MicrobeRelation::THROUGH,

        // has one
        HAS_ONE = MicrobeRelation::ONE,

        // has many
        HAS_MANY = MicrobeRelation::ALL,

        // belongs to
        BELONGS_TO = MicrobeRelation::OWN,
        
        // many to many
        MANY_TO_MANY = 0;

    /** @var MicrobeConnection[] $_connections */
    protected $_connections = [];

    /** @var MicrobeModelManager $_modelManager */
    protected $_modelManager;

    /** @var MicrobeService[] $_services */
    protected $_services = [];

    /** @var callable|null $_exceptionHandlerOnConnection */
    protected $_exceptionHandlerOnConnection;

    /** @var callable|null $_exceptionHandlerOnCursorFetch */
    protected $_exceptionHandlerOnCursorFetch;

    /** @var callable|null $_exceptionHandlerOnRequest */
    protected $_exceptionHandlerOnRequest;

    /** @var ServiceValidator $Validator */
    public $Validator;

    /**
     * Get [Microbe] instance.
     *
     * @return Microbe
     */
    public static function microbe()
    {
        return Microbe::getInstanceShared();
    }

    /**
     *
     */
    public function __construct()
    {
        $this->_modelManager = new MicrobeModelManager($this);
        $this->Validator = $this->_services['validator'] = new ServiceValidator();
    }

    /**
     * @return MicrobeConnection
     */
    public function getConnection($alias)
    {
        if (isset($this->_connections[$alias])) {
            return $this->_connections[$alias];
        }

        throw new ConnectionNotExistsException('On: ' . $alias);
    }

    /**
     * Set connection.
     *
     * @param string $alias Connection alias.
     * @param array|MicrobeConnection $mixedCredentials Credentials or custom connection.
     * @param string $connectionType Connection type (type of adapter).
     * @param null $connectionOptions Connection options.
     *
     * @return $this
     */
    public function setConnection($alias, $mixedCredentials, $connectionType = self::MYSQL, $connectionOptions = null)
    {
        if ($mixedCredentials instanceof MicrobeConnection) {
            $this->_connections[$alias] = $mixedCredentials;
        } else {
            $this->_connections[$alias] = new MicrobeConnection($this, array_merge(
                $mixedCredentials,
                [
                    'adapter' => null,
                    'alias' => $alias,
                    'options' => $connectionOptions,
                    'type' => isset($connectionOptions['vc']) ? $connectionType . 'VC' : $connectionType,
                ]
            ));
        }

        return $this;
    }

    /**
     * @return MicrobeAdapter
     */
    public function getConnectionAdapter($alias)
    {
        return $this->getConnection($alias)->getAdapter();
    }

    /**
     * @return null|callable
     */
    public function getExceptionHandlerOnConnection()
    {
        return $this->_exceptionHandlerOnConnection;
    }

    /**
     * @return null|callable
     */
    public function getExceptionHandlerOnCursorFetch()
    {
        return $this->_exceptionHandlerOnCursorFetch;
    }

    /**
     * @return null|callable
     */
    public function getExceptionHandlerOnRequest()
    {
        return $this->_exceptionHandlerOnRequest;
    }

    /**
     * @return MicrobeModelManager
     */
    public function getModelManager()
    {
        return $this->_modelManager;
    }

    /**
     * @return MicrobeModelMetadata
     */
    public function getModelMetadata($class)
    {
        return $this->_modelManager->getModelMetadata($class);
    }

    /**
     * @return ServiceValidator
     */
    public function getService($alias)
    {
        if (isset($this->_services[$alias])) {
            return $this->_services[$alias];
        }

        throw new NotImplementedException('Service ' . $alias);
    }

    /**
     * @return $this
     */
    public function setExceptionHandlerOnConnection(callable $value)
    {
        $this->_exceptionHandlerOnConnection = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function setExceptionHandlerOnCursorFetch(callable $value)
    {
        $this->_exceptionHandlerOnCursorFetch = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function setExceptionHandlerOnRequest(callable $value)
    {
        $this->_exceptionHandlerOnRequest = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function setService($alias, MicrobeService $mixed)
    {
        $this->{$alias} = $this->_services[$alias] = $mixed;

        return $this;
    }

    /**
     *
     */
    public function refresh($alias = null)
    {
        if ($alias) {
            return $this->getConnectionAdapter($alias)->connectionRefresh();
        }

        /** @var MicrobeConnection $_connection */
        foreach ($this->_connections as $_connection) {
            $_connection->getAdapter()->connectionRefresh();
        }

        return true;
    }
}

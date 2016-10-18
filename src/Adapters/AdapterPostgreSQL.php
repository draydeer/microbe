<?php

namespace Microbe\Adapters;

use Microbe\Adapters\Criterias\CriteriaPostgreSQL;
use Microbe\Adapters\Cursors\CursorMySQL;
use Microbe\Adapters\Dialects\Traits\TraitDialectPostgreSQL;
use Microbe\Adapters\ExceptionClasses\PostgreSQL;
use Microbe\Exceptions\Adapter\AdapterIncompatibleConditionException;
use Microbe\Microbe;
use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;

/**
 * Class AdapterPostgreSQL
 * @package Microbe\Adapters
 */
class AdapterPostgreSQL extends AdapterMySQL
{
    use TraitDialectPostgreSQL;

    /**
     *
     */
    protected function onInitConnection()
    {
        $this->_connected = false;

        while (1) {
            try {
                $this->_client = new \PDO(
                    'pgsql:dbname=' . $this->_connection['name'] . ';host=' . $this->_connection['host'],
                    $this->_connection['user'],
                    $this->_connection['pass'],
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_PERSISTENT => $this->getParam(
                            $this->_connection['options'],
                            'persistent',
                            true
                        ),
                    ]
                );

                $this->_db = $this->_connection['name'];
                $this->_dbTarget = null;

                return $this;
            } catch (\Exception $e) {
                if ($this->processExceptionOnConnection($e) === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     *
     */
    public static function getCondition($model, $param = null)
    {
        if ($model instanceof CriteriaPostgreSQL) {
            return $model->getCondition();
        }

        if ($model instanceof MicrobeModelMetadata) {
            if (is_array($param) === false || count($param) === 0) {
                return static::getDialectForEOL();
            }

            $result = CriteriaPostgreSQL::compileParametrized(
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
    public static function getFetchable($cursor, $timeout = 0)
    {
        return CursorMySQL::getFetchable($cursor, $timeout = 0);
    }

    /**
     *
     */
    public static function getSchema($table, $tableSchema = null)
    {
        return $tableSchema . '.' . $table;
    }

    /**
     *
     */
    public function getType()
    {
        return Microbe::POSTGRESQL;
    }

    /**
     *
     */
    public function examine(\Exception $e)
    {
        return PostgreSQL::examine($e);
    }

}

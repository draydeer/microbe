<?php

namespace Microbe;

/**
 * Class MicrobeModelMetadata
 * @package Microbe
 *
 * @property MicrobeAdapter $connection
 * @property MicrobeAdapter $connectionWrite
 */
class MicrobeModelMetadata extends MicrobeBase
{

    /** @var string $alias */
    public $alias;

    /** @var string $alias */
    public $class;

    /** @var MicrobeConnection $connection */
    public $connection;

    /** @var MicrobeConnection $connectionWrite */
    public $connectionWrite;

    /** @var Microbe $microbe */
    public $microbe;

    /** @var MicrobeModel $motherObject */
    public $motherObject;

    /** @var string $pk */
    public $pk;

    /** @var array $relations */
    public $relations;

    /** @var string $schema */
    public $schema;

    /**
     * @return MicrobeModelManager
     */
    public function getModelManager()
    {
        return $this->microbe->getModelManager();
    }

    /**
     *
     */
    public function setAlias($value)
    {
        $this->alias = $value;

        return $this;
    }

    /**
     *
     */
    public function setClass($value)
    {
        $this->class = $value;

        return $this;
    }

    /**
     *
     */
    public function setConnection($value)
    {
        $this->connection = $value;

        return $this;
    }

    /**
     *
     */
    public function setConnectionWrite($value)
    {
        $this->connectionWrite = $value;

        return $this;
    }

    /**
     *
     */
    public function setMicrobe(Microbe $value)
    {
        $this->microbe = $value;

        return $this;
    }

    /**
     *
     */
    public function setPK($value)
    {
        $this->pk = $value;

        return $this;
    }

    /**
     *
     */
    public function setRelations($value)
    {
        $this->relations = $value;

        return $this;
    }

    /**
     *
     */
    public function setSchema($value)
    {
        $this->schema = $value;

        return $this;
    }

}

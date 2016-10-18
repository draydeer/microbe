<?php

namespace Microbe;

use Microbe\Exceptions\Relation\RelationUnknownException;
use Microbe\Exceptions\Relation\RelationUnrecognizedException;

/**
 * Class MicrobeModelManager
 * @package Microbe
 */
class MicrobeModelManager extends MicrobeBase
{

    /** @var array $_container */
    protected $_container = [];

    /** @var MicrobeModelMetadata[] $_containerMeta */
    protected $_containerMeta = [];

    /** @var string $_ns */
    protected $_ns = null;

    /**
     *
     */
    public function __construct(Microbe $microbe)
    {
        $this->_microbe = $microbe;
    }

    /**
     *
     */
    public function getModelMetadata($class)
    {
        return isset($this->_containerMeta[$class]) ? $this->_containerMeta[$class] : static::setModelMetadata($class);
    }

    /**
     *
     */
    public function getModelMetadataOfCaller()
    {
        return $this->getModelMetadata(get_called_class());
    }

    /**
     * @param string $class
     */
    public function setModelMetadata($class)
    {
        if (class_exists($class)) {
            $entity = new MicrobeModelMetadata();

            $entity->alias = static::whoIs($class);
            $entity->class = $class;
            $entity->connection = $class::getConnection();
            $entity->connectionWrite = $class::getConnectionWrite();
            $entity->microbe = Microbe::getInstanceShared();
            $entity->pk = $class::getPK();
            $entity->relations = [];
            $entity->schema = $class::getSchema();

            $this->_containerMeta[$class] = $entity;

            $entity->motherObject = $this->load($class);

            foreach ($class::getRelationsAll() as $k =>&$relation) {
                $entity->relations[$k] = $relation;

                $relation =&$entity->relations[$k];

                if (count($relation) < 3) {
                    throw new RelationUnrecognizedException('On: ' . $class . ' -> ' . $k);
                }

                if (isset($relation[MicrobeRelation::IND_CLASS]) === false) {
                    $relation[MicrobeRelation::IND_CLASS] = $class;
                }

                if (isset($relation[MicrobeRelation::IND_RELATION]) === false) {
                    $relation[MicrobeRelation::IND_RELATION] = static::whoAmI();
                }

                switch ($relation[MicrobeRelation::IND_TYPE]) {
                    case MicrobeRelation::ONE:
                        $relation[MicrobeRelation::IND_IS_PK] = false;//$V[MicrobeRelation::IND_L] === true;

                        if ($relation[MicrobeRelation::IND_L] === true) {
                            $relation[MicrobeRelation::IND_L] = $class::getPK();
                        }

                        if ($relation[MicrobeRelation::IND_R] === true && $class !== $relation[MicrobeRelation::IND_CLASS]) {
                            $relation[MicrobeRelation::IND_R] = $this->getModelMetadata($relation[MicrobeRelation::IND_CLASS])->pk;
                        }

                        break;

                    case MicrobeRelation::OWN:
                        $relation[MicrobeRelation::IND_IS_PK] = false;//$V[MicrobeRelation::IND_R] === true;

                        if ($relation[MicrobeRelation::IND_L] === true) {
                            $relation[MicrobeRelation::IND_L] = $class::getPK();
                        }

                        if ($relation[MicrobeRelation::IND_R] === true && $class !== $relation[MicrobeRelation::IND_CLASS]) {
                            $relation[MicrobeRelation::IND_R] = $this->getModelMetadata($relation[MicrobeRelation::IND_CLASS])->pk;
                        }

                        break;

                    case MicrobeRelation::ALL:
                        $relation[MicrobeRelation::IND_IS_PK] = $relation[MicrobeRelation::IND_L] === true;

                        if ($relation[MicrobeRelation::IND_L] === true) {
                            $relation[MicrobeRelation::IND_L] = $class::getPK();
                        }

                        if ($relation[MicrobeRelation::IND_R] === true && $class !== $relation[MicrobeRelation::IND_CLASS]) {
                            $relation[MicrobeRelation::IND_R] = $this->getModelMetadata($relation[MicrobeRelation::IND_CLASS])->pk;
                        }

                        break;

                    case MicrobeRelation::THROUGH:
                        $relation[MicrobeRelation::IND_IS_PK] = false;

                        if ($relation[MicrobeRelation::IND_R] === null) {
                            $relation[MicrobeRelation::IND_R] = $relation[MicrobeRelation::IND_L];
                        }

                        break;

                    default:
                        throw new RelationUnknownException('On: ' . $class . ' -> ' . $relation[3]);
                }
            }

            return $entity;
        }

        throw new \Exception('class not exists: ' . $class);
    }

    /**
     *
     */
    public function load($class, $ns = null)
    {
        return clone $this->loadStatic($class, $ns);
    }

    /**
     * @param MicrobeModel $class
     */
    public function loadStatic($class, $ns = null)
    {
        if ($ns) {
            $class = '\\' . $ns . '\\' . $class;
        }

        if (isset($this->_container[$class])) {
            return $this->_container[$class];
        }

        if (class_exists($class)) {

            /** @var MicrobeModel $_model */
            $_model = $this->_container[$class] = new $class();

            $_model->initialize();

            return $_model;
        }

        throw new \Exception('model not exists ' . $class);
    }

}

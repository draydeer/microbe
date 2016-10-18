<?php

namespace Microbe;

use Microbe\Interfaces\ArrayPresentationInterface;
use Microbe\MicrobeMetadata;

/**
 * Class MicrobeCollectionBulk
 * @package Microbe
 */
class MicrobeCollectionBulk extends MicrobeCollection
{

    /**
     *
     */
    public function setRelationLeft(MicrobeRelation $relation = null)
    {
        if ($relation) {
            $this->_relation = $relation;
        }

        return $relation;
    }

    /**
     *
     */
    public function add($value, $index = null)
    {
        if ($value instanceof MicrobeModel) {
            $_state = $value->getState();

            unset($_state[$value->getPK()]);

            $value = $_state;
        }

        if (is_array($value)) {
            if ($index !== null) {
                return $this->_container[$index] = $value;
            }

            return $this->_container[] = $value;
        }

        return null;
    }

    /**
     *
     */
    public function put($extra = null)
    {
        if ($this->_metadata && empty($this->_container) === false) {
            $_metadata = $this->getMicrobe()->getModelMetadata($this->_relation->getDescriptor()[3]);
            $_pk = $this->_relation->getL()->getStateFieldPK();
            $_pkReferenced = $this->_relation->getDescriptor()[2];

            foreach ($this->_container as&$V) {
                $V[$_pkReferenced] = $_pk;
            }

            $_result = $_metadata->connectionWrite->_insBulk($_metadata, $this->_container);

            $this->clear();

            return $_result;
        }

        return false;
    }

    /**
     *
     */
    public function putThrough($mixed)
    {
        if (empty($this->_container) === false) {
            $_metadata = $mixed instanceof MicrobeMetadata ? $mixed->getMetadata() : $this->getMicrobe()->getModelMetadata($mixed);

            $_result = $_metadata->connectionWrite->_insBulk($_metadata, $this->_container);

            $this->clear();

            return $_result;
        }

        return false;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_container;
    }

    /**
     * @return MicrobeCollection
     */
    public function toCollection($fieldFetchBy = null)
    {
        return $this;
    }
}

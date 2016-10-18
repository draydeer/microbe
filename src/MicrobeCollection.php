<?php

namespace Microbe;

use Microbe\Interfaces\ArrayPresentationInterface;
use Microbe\MicrobeRelation;

/**
 * Class MicrobeCollection
 * @package Microbe
 */
class MicrobeCollection extends MicrobeResultSet
{

    /** @var MicrobeModel[]  */
    protected $_container = [];

    /**
     *
     */
    public function __construct(MicrobeModelMetadata $metadata, $mixed = null)
    {
        $this->_metadata = $metadata;

        if ($mixed instanceof MicrobeModel) {
            $this->_container = [$mixed];
        } else
        if (is_array($mixed)) {
            $this->_container = $mixed;
        }
    }

    /**
     *
     */
    public function __get($k)
    {

    }

    /**
     *
     */
    public function __set($k, $v)
    {

    }

    /**
     *
     */
    public function getIterator()
    {
        foreach ($this->_container as $k => $state) {
            yield $k => $state;
        }
    }

    /**
     *
     */
    public function setRelationLeft(MicrobeRelation $relation)
    {
        if ($relation) {
            $this->_relation = $relation;

            $relationLeft = $relation->getL();

            $this->_metadata = $relationLeft->getMetadata();

            foreach ($this->_container as $state) {
                $state->setRelated($this->_metadata->alias, $relationLeft);
            }
        }

        return $relation;
    }

    /**
     *
     */
    public function&offsetGet($k)
    {
        static $null = null;

        if (isset($this->_container[$k])) {
            return $this->_container[$k];
        }

        return $null;
    }

    /**
     *
     */
    public function offsetExists($k)
    {
        return isset($this->_container[$k]);
    }

    /**
     *
     */
    public function offsetSet($k, $v)
    {
        if ($v instanceof MicrobeModel) {
            if ($k === null) {
                $this->_container[] = $v;

                return;
            }

            $this->_container[$k] = $v;
        }
    }

    /**
     *
     */
    public function offsetUnset($k)
    {
        unset($this->_container[$k]);
    }

    /**
     *
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     *
     */
    public function add($value, $index = null)
    {
        if ($value instanceof MicrobeModel) {
            if ($this->_relation) {
                $value->setRelated($this->_metadata->alias, $this->_relation->getL());
            }

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
    public function clear()
    {
        $this->_container = [];
    }

    /**
     *
     */
    public function count()
    {
        return count($this->_container);
    }

    /**
     *
     */
    public function del($extra = null)
    {
        foreach ($this->_container as $state) {
            if ($state->del($extra) === false) {
                return false;
            };
        }

        return true;
    }

    /**
     *
     */
    public function put($extra = null)
    {
        foreach ($this->_container as $state) {
            if ($state->put(true) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->_container as $state) {
            if ($state instanceof ArrayPresentationInterface) {
                $result[] = $state->toArray();
            }
        }

        return $result;
    }

    /**
     * @return MicrobeCollection
     */
    public function toCollection($fieldFetchBy = null)
    {
        return $this;
    }

}

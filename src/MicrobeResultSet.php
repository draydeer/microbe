<?php

namespace Microbe;

use Microbe\Exceptions\NotImplementedException;
use Microbe\Interfaces\ArrayPresentationInterface;
use Microbe\Traits\Ext\GetInstanceTrait;

/**
 * Class MicrobeResultSet
 * @package Microbe
 */
class MicrobeResultSet extends MicrobeState implements \ArrayAccess, \Countable, \IteratorAggregate, ArrayPresentationInterface
{
    use GetInstanceTrait;

    const HYD_ARR = 0;

    const HYD_OBJ = 1;

    /**  @var MicrobeCursor $_cursor */
    protected $_cursor = null;

    /** @var MicrobeRelation $_relation */
    protected $_relation = null;

    /** @var int $_hydration */
    protected $_hydration = self::HYD_ARR;

    /**
     *
     */
    public function __construct(MicrobeModelMetadata $metadata, $mixed = null)
    {
        $this->_metadata = $metadata;

        if ($mixed instanceof MicrobeCursor) {
            $this->_cursor = $mixed;
        }
    }

    /**
     *
     */
    public function getIterator()
    {
        if ($this->_cursor) {
            return $this->_cursor;
        }
        
        return [];
    }

    /**
     *
     */
    public function offsetGet($k)
    {
        return null;
    }

    /**
     *
     */
    public function offsetExists($k)
    {
        return false;
    }

    /**
     *
     */
    public function offsetSet($k, $v)
    {
        return null;
    }

    /**
     *
     */
    public function offsetUnset($k)
    {
        return null;
    }

    /**
     *
     */
    public function getCursor()
    {
        return $this->_cursor;
    }

    /**
     *
     */
    public function getRelationLeft()
    {
        return $this->_relation
            ? $this->_relation->getL()
            : null;
    }

    /**
     *
     */
    public function setRelationLeft(MicrobeRelation $relation)
    {
        $this->_relation = $relation;

        return $this;
    }

    /**
     *
     */
    public function getRelated($key, $param = null, $extra = null)
    {
        return null;
    }

    /**
     *
     */
    public function setRelated($key, MicrobeState $value)
    {
        return null;
    }

    /**
     *
     */
    public function getStateField($key)
    {
        throw new NotImplementedException();
    }

    /**
     *
     */
    public function setStateField($key, $value)
    {
        throw new NotImplementedException();
    }

    /**
     *
     */
    public function setHydrationArr()
    {
        $this->_hydration = self::HYD_ARR;

        return $this;
    }

    /**
     *
     */
    public function setHydrationObj()
    {
        $this->_hydration = self::HYD_OBJ;

        return $this;
    }

    /**
     *
     */
    public function add($value, $index = null)
    {
        throw new NotImplementedException();
    }

    /**
     *
     */
    public function chunked($limit = MicrobeCursor::MOD_CONTINUOUS)
    {
        if ($this->_cursor) {
            $this->_cursor->setChunked($limit);
        }

        return $this;
    }

    /**
     *
     */
    public function count()
    {
        return $this->_cursor ? $this->_cursor->getAggCount() : 0;
    }

    /**
     *
     */
    public function hydrateAsArr()
    {
        return [];
    }

    /**
     *
     */
    public function hydrateAsObj()
    {
        return [];
    }

    /**
     *
     */
    public function paginate(
        $index,
        $limit = 15,
        $extra = null,
        $forceAggCount = true
    )
    {
        if ($index < 1) {
            $index = 1;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        $cursor = $this->getCursor();

        if ($cursor) {
            if ($extra) {
                $cursor->queryExtraMerge($extra);
            }

            $result = new \stdClass();

            $result->limit = $limit;

            $result->total = $forceAggCount
                ? $cursor->getMetadata()->connection->_aggCount($cursor->getMetadata(), $cursor->getQueryParam())
                : null;

            $result->count = $result->total > 0
                ? intval(($result->total - 1) / $limit) + 1
                : 0;

            $result->index = $forceAggCount
                ? ($index < $result->count ? $index : $result->count)
                : $index;

            $extra =&$cursor->getQueryExtra();

            $extra['limit'] = $limit;

            $extra['offset'] = $result->index > 0 ? $result->index * $limit - $limit : 0;

            $result->items = $this->toArray();

            return $result;
        }

        return null;
    }

    /**
     *
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->getIterator() as $state) {
            $result[] = $state instanceof ArrayPresentationInterface ? $state->toArray() : (array) $state;
        }

        return $result;
    }

    /**
     * @return MicrobeCollection
     */
    public function toCollection($fieldFetchBy = null)
    {
        if ($this->_relation) {
            $result = new MicrobeCollection($this->_metadata, $this->_relation);
        } else {
            $result = new MicrobeCollection($this->_metadata);
        }

        if ($fieldFetchBy) {
            if ($this->_relation) {
                $relationLeft = $this->_relation->getL();

                foreach ($this->getIterator() as $state) {
                    $result->add($state, $state->{$fieldFetchBy})->setRelated($this->_metadata->alias, $relationLeft);
                }
            } else {
                foreach ($this->getIterator() as $state) {
                    $result->add($state, $state->{$fieldFetchBy});
                }
            }
        } else {
            if ($this->_relation) {
                $relationLeft = $this->_relation->getL();

                foreach ($this->getIterator() as $state) {
                    $result->add($state)->setRelated($this->_metadata->alias, $relationLeft);
                }
            } else {
                foreach ($this->getIterator() as $state) {
                    $result->add($state);
                }
            }
        }

        return $result;
    }
}

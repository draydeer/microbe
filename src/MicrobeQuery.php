<?php

namespace Microbe;

use Microbe\Exceptions\Relation\RelationUnknownException;

/**
 * Class MicrobeQuery
 * @package Microbe
 */
class MicrobeQuery extends MicrobeMetadata implements \IteratorAggregate
{

    /** @var array $_q */
    protected $_q = [];

    /**
     *
     */
    protected function doJoinIn($alias, array&$pkJoinIn, array&$result)
    {
        foreach ($this->_q as $k => $v) {
            switch ($v[MicrobeRelation::IND_TYPE]) {
                case MicrobeRelation::ONE:
                case MicrobeRelation::OWN:
                    $l = $v[MicrobeRelation::IND_L];
                    $r = $v[MicrobeRelation::IND_R];
                    $metadata = $this->getMicrobe()->getModelMetadata($v[MicrobeRelation::IND_CLASS]);
                    $res = [];

                    if ($v[MicrobeRelation::IND_IS_PK]) {
                        foreach ($pkJoinIn[$l] as&$temp_1) {
                            $temp_1 = $metadata->connection->getPKValue($temp_1);
                        }
                    }

                    foreach ($metadata->connection->_selIn($metadata, $pkJoinIn[$l], $r) as $temp_a) {
                        $res[(string) $temp_a[$r]] = $temp_a;
                    }

                    foreach ($result as&$temp_b) {
                        $_key = (string) $temp_b[$alias][$l];

                        if (isset($res[$_key])) {
                            $temp_b[$k] = $res[$_key];
                        }
                    }

                    break;
            }
        }
    }

    /**
     *
     */
    protected function doQuery($result)
    {
        $index = 1;
        $metadata = $this->_metadata;
        $pk = $this->_metadata->pk;
        $pkJoinIn = [];
        $result = [];

        foreach ($this->_q as $relation) {
            $pkJoinIn[$relation[MicrobeRelation::IND_L]] = [];
        }

        foreach ($result as $k => $v) {
            foreach ($this->_q as&$relation) {
                $pkJoinIn[$relation[MicrobeRelation::IND_L]][] = $v[$relation[MicrobeRelation::IND_L]];
            }

            $result[(string) $v[$pk]] = [
                $metadata->alias => $v,
            ];

            if (count($pkJoinIn) >= 1024) {
                $this->doJoinIn($metadata->alias, $pkJoinIn, $result);

                foreach ($result as $k => $v) {
                    yield $index ++ => $v;
                }

                $result = [];
                $pkJoinIn = [];
            }
        }

        if (count($pkJoinIn)) {
            $this->doJoinIn($metadata->alias, $pkJoinIn, $result);

            foreach ($result as $k => $v) {
                yield $index ++ => $v;
            }
        }
    }

    /**
     *
     */
    public function __construct(MicrobeModelMetadata $metadata)
    {
        $this->_metadata = $metadata;
    }

    /**
     *
     */
    public function getIterator()
    {
        return $this->query();
    }

    /**
     *
     */
    public function joinIn($relation, $extra = null)
    {
        if (isset($this->_metadata->relations[$relation])) {
            switch ($this->_metadata->relations[$relation][MicrobeRelation::IND_TYPE]) {
                case MicrobeRelation::ONE:
                case MicrobeRelation::OWN:
                    $this->_q[$relation] = $this->_metadata->relations[$relation];

                    return $this;
            }
        }

        throw new RelationUnknownException();
    }

    /**
     *
     */
    public function query($param = null, $extra = null)
    {
        return new MicrobeResultSet($this->_metadata, $this->_metadata->connection->getCursor(
            $this->_metadata,
            $param,
            $extra,
            function($r) {
                return $this->doQuery($r);
            }
        ));
    }

}

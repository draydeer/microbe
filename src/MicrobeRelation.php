<?php

namespace Microbe;

/**
 * Class MicrobeRelation
 * @package Microbe
 */
class MicrobeRelation
{

    const ONE = 0;

    const OWN = 1;

    const ALL = 2;

    const THROUGH = 3;

    const IND_TYPE = 0;

    const IND_L = 1;

    const IND_R = 2;

    const IND_CLASS = 3;

    const IND_RELATION = 4;

    const IND_IS_PK = 5;

    /** @var null $_desc */
    protected $_desc;

    /** @var MicrobeState $_l */
    protected $_l;

    /** @var MicrobeState $_r */
    protected $_r;

    /** @var string $_type */
    protected $_type;

    /**
     *
     */
    public static function getOne(MicrobeModel $l, MicrobeModel $r)
    {
        return new static(
            $l,
            $r,
            self::ONE
        );
    }

    /**
     *
     */
    public static function getOwn(MicrobeModel $l, MicrobeModel $r)
    {
        return new static(
            $l,
            $r,
            self::OWN
        );
    }

    /**
     *
     */
    public static function getAll(MicrobeModel $l, MicrobeResultSet $r)
    {
        return new static(
            $l,
            $r,
            self::OWN
        );
    }

    /**
     *
     */
    public function __construct(
        MicrobeState $l,
        MicrobeState $r,
        $type,
        $descriptor = null
    )
    {
        $this->_desc = $descriptor;
        $this->_l = $l;
        $this->_r = $r;
        $this->_type = $type;
    }

    /**
     *
     */
    public function getDescriptor()
    {
        return $this->_desc;
    }

    /**
     * @return MicrobeModel
     */
    public function getL()
    {
        return $this->_l;
    }

    /**
     * @return MicrobeModel|MicrobeResultSet
     */
    public function getR()
    {
        return $this->_r;
    }

    /**
     *
     */
    public function getType()
    {
        return $this->_type;
    }

}

<?php

namespace Microbe;

use Microbe\Interfaces\FlagInterface;
use Microbe\Interfaces\StateFieldInterface;
use Microbe\Interfaces\RelatedInterface;

/**
 * Class MicrobeState
 * @package Microbe
 */
abstract class MicrobeState extends MicrobeMetadata implements FlagInterface, StateFieldInterface, RelatedInterface
{

    /** @var int $_flags */
    protected $_flags = 0;

    /** @var mixed $_state */
    protected $_state;

    /** @var mixed $_stateChanged */
    protected $_stateChanged;

    /** @var mixed $_stateRelated */
    protected $_stateRelated;

    /**
     *
     */
    public function getFlag($value, $forceReset = false)
    {
        $result = $this->_flags & (int) $value;

        if ($forceReset) {
            $this->_flags&=~(int) $value;
        }

        return $result;
    }

    /**
     *
     */
    public function setFlag($value, $forceReset = false)
    {
        if ($forceReset) {
            $this->_flags&=~(int) $value;
        } else {
            $this->_flags|= (int) $value;
        }

        return $this;
    }

    /**
     *
     */
    public function del($extra = null)
    {
        return false;
    }

    /**
     *
     */
    public function put($extra = null)
    {
        return false;
    }

}

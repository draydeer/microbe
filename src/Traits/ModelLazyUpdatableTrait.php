<?php

namespace Microbe\Traits;

use Microbe\MicrobeModel;

/**
 * Class ModelLazyUpdatableTrait
 * @package Microbe\Traits
 */
trait ModelLazyUpdatableTrait
{

    /*
     *
     */
    private
        $_inPut         = false;

    /**
     *
     */
    public function put($extra = null)
    {
        if ($extra !== true || $this->_dirty !== MicrobeModel::DRT_TRANSIENT) {
            if ($this->_dirty === MicrobeModel::DRT_LOCKED) {
                return $this->_dirty;
            }

            $this->_inPut = true;

            return true;
        }

        return parent::put($extra);
    }

    /**
     *
     */
    public function putOut($extra = null)
    {
        if ($this->_inPut) {
            $this->_inPut = false;

            return parent::put($extra);
        }

        return true;
    }
}

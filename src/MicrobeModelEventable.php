<?php

namespace Microbe;

/**
 * Class MicrobeModelEventable
 * @package Microbe
 */
abstract class MicrobeModelEventable extends MicrobeModel
{

    /**
     *
     */
    public function onDelete()
    {
        return true;
    }

    /**
     *
     */
    public function onDeleteAfter($result)
    {
        return true;
    }

    /**
     *
     */
    public function onInsert()
    {
        return true;
    }

    /**
     *
     */
    public function onInsertAfter($result)
    {
        return true;
    }

    /**
     *
     */
    public function onStabilize()
    {
        return true;
    }

    /**
     *
     */
    public function onUpdate()
    {
        return true;
    }

    /**
     *
     */
    public function onUpdateAfter($result)
    {
        return true;
    }

    /**
     *
     */
    public function put($extra = null)
    {
        if ($this->_dirty === self::DRT_LOCKED) {
            return true;
        }

        $dirty = $this->_dirty;

        $this->_dirty = self::DRT_LOCKED;

        foreach ($this->_stateRelated as $k => $state) {
            $relation = $this->_metadata->relations[$k];

            /**
             * @var MicrobeRelation $state
             */
            if ($state->getType() === MicrobeRelation::OWN) {
                $state->getR()->put(true);
                $state->getL()->setStateField($relation[1], $state->getR()->getStateField($relation[2]));
            }
        }

        switch ($dirty) {
            case self::DRT_TRANSIENT:
                if ($this->onInsert() === false) {
                    return false;
                }

                $result = $this->_metadata->connectionWrite->_ins(
                    $this->_metadata,
                    $this->_state
                );

                if ($result) {
                    $this->_state[$this->_metadata->pk] = $result;
                }

                $this->onInsertAfter(empty($result) === false);

                break;

            case self::DRT_TRACKING:
                if ($this->onUpdate() === false) {
                    return false;
                }

                $result = $this->_metadata->connectionWrite->_updPartialPK(
                    $this->_metadata,
                    $this->_state
                );

                if ($result) {

                }

                $this->onUpdateAfter(empty($result) === false);

                break;

            default:
                $result = 1;
        }

        if (empty($result)) {
            $this->_dirty = $dirty;

            return $this->_dirty === self::DRT_STABLE;
        }

        foreach ($this->_stateRelated as $k => $state) {
            $relation = $this->_metadata->relations[$k];

            /**
             * @var MicrobeRelation $state
             */
            switch ($state->getType()) {
                case MicrobeRelation::ONE:
                    $state->getR()->setStateField($relation[2], $state->getL()->getStateField($relation[1]));
                    $state->getR()->put(true);

                    break;

                case MicrobeRelation::ALL:
                    $state->getR()->put();

                    break;
            }
        }

        $this->_dirty = self::DRT_STABLE;

        if ($dirty !== self::DRT_STABLE) {
            $this->onStabilize();
        }

        return true;
    }

}

<?php

namespace Microbe;

use Microbe\Exceptions\Metadata\MetadataIncompatibleException;
use Microbe\Exceptions\Relation\RelationIncompatibleEntityException;
use Microbe\Exceptions\Relation\RelationUnknownException;
use Microbe\Interfaces\ArrayPresentationInterface;
use Microbe\Traits\Ext\GetInstanceTrait;

/**
 * Class MicrobeModel
 * @package Microbe
 */
abstract class MicrobeModel extends MicrobeState implements ArrayPresentationInterface
{
    use GetInstanceTrait;

    const ONE = MicrobeRelation::ONE;

    const ALL = MicrobeRelation::ALL;

    const OWN = MicrobeRelation::OWN;

    const THROUGH = MicrobeRelation::THROUGH;

    const DRT_STABLE = 0;

    const DRT_LOCKED = 1;

    const DRT_TRANSIENT = 2;

    const DRT_TRACKING = 3;

    const FLG_NEW = 1;

    /** @var string $MicConnection */
    protected static $MicConnection  = 'default';

    /** @var string $MicConnectionWrite */
    protected static $MicConnectionWrite = null;

    /** @var bool $MicIntegrity */
    protected static $MicIntegrity = false;

    /** @var string $MicField */
    protected static $MicField = null;

    /** @var array $MicPreset */
    protected static $MicPreset = [];

    /** @var array $MicRelations */
    protected static $MicRelations = [];

    /** @var string $MicSchema */
    protected static $MicSchema = null;

    /** @var string $MicTable */
    protected static $MicTable = null;

    /** @var array $MicValidators */
    protected static $MicValidators = [];

    /** @var bool $MicVC */
    protected static $MicVC = false;

    /** @var string $PK */
    protected static $PK = null;

    /** @var null $_null */
    protected static $_null = null;

    /** @var int $_dirty */
    protected $_dirty = self::DRT_TRANSIENT;

    /** @var array|null $_state */
    protected $_state = [];

    /** @var array $_stateChanged */
    protected $_stateChanged = [];

    /** @var array $_stateRelated */
    protected $_stateRelated = [];

    /**
     *
     */
    public static function getCondition($alias, $param)
    {
        return static::getConnection()->getCondition($alias, $param);
    }

    /**
     * @return MicrobeCollectionBulk
     */
    public static function getCollectionBulk()
    {
        return new MicrobeCollectionBulk(static::microbe()->getModelMetadata(get_called_class()));
    }

    /**
     * @return MicrobeAdapter
     */
    public static function getConnection()
    {
        return static::microbe()->getConnectionAdapter(static::$MicConnection);
    }

    /**
     * @return MicrobeAdapter
     */
    public static function getConnectionWrite()
    {
        return static::microbe()->getConnectionAdapter(static::$MicConnectionWrite ? static::$MicConnectionWrite : static::$MicConnection);
    }

    /**
     *
     */
    public static function getCriteria()
    {
        return static::microbe()->getConnectionAdapter(static::$MicConnection)->getCriteria(static::getSchema());
    }

    /**
     *
     */
    public static function getPK()
    {
        return static::$PK === null ? static::getConnection()->getPK() : static::$PK;
    }

    /**
     *
     */
    public static function getQuery()
    {
        return new MicrobeQuery(static::microbe()->getModelMetadata(get_called_class()));
    }

    /**
     *
     */
    public static function getRelation($alias)
    {
        return static::getParam(static::$MicRelations, $alias);
    }

    /**
     *
     */
    public static function&getRelationsAll($alias = null)
    {
        return static::$MicRelations;
    }

    /**
     *
     */
    public static function getSchema($alias = null)
    {
        if ($alias === null) {
            if (static::$MicTable === null) {
                return static::getConnection()->getSchema(
                    static::whoAmI(),
                    static::$MicSchema
                );
            } else {
                return static::getConnection()->getSchema(
                    static::$MicTable,
                    static::$MicSchema
                );
            }
        }

        return $alias;
    }

    /**
     *
     */
    public static function getTargetSource()
    {
        return static::microbe()->getConnectionAdapter(static::$MicConnection)->getClientTargetSource(
            static::microbe()->getModelMetadata(get_called_class())
        );
    }

    /**
     * 
     */
    public static function setOne(
        $alias,
        $fieldForeign,
        $class = null,
        $fieldPK = null
    )
    {
        if ($class === null) {
            $class.= static::whoIsMyNS(1) . $alias;
        }

        $metadata = static::microbe()->getModelMetadata(get_called_class());

        $metadata->relations[$alias] = [
            MicrobeRelation::ONE,
            $fieldPK ? $fieldPK : static::getPK(),
            $fieldForeign,
            $class,
            $alias,
            $fieldPK === null
        ];
    }

    /**
     *
     */
    public static function setOwn(
        $alias,
        $fieldForeign,
        $class = null,
        $fieldPK = null
    )
    {
        if ($class === null) {
            $class.= static::whoIsMyNS(1) . $alias;
        }

        $metadata = static::microbe()->getModelMetadata(get_called_class());

        $metadata->relations[$alias] = [
            MicrobeRelation::OWN,
            $fieldPK ? $fieldPK : static::getPK(),
            $fieldForeign,
            $class,
            $alias,
            $fieldPK === null
        ];
    }

    /**
     *
     */
    public static function setAll(
        $alias,
        $fieldForeign,
        $class = null,
        $fieldPK = null
    )
    {
        if ($class === null) {
            $class.= static::whoIsMyNS(1) . $alias;
        }

        $metadata = static::microbe()->getModelMetadata(get_called_class());

        $metadata->relations[$alias] = [
            MicrobeRelation::ALL,
            $fieldPK ? $fieldPK : static::getPK(),
            $fieldForeign,
            $class,
            $alias,
            $fieldPK === null
        ];
    }

    /**
     * Find all records (all or by condition).
     *
     * @param array $param Parameters of condition.
     * @param array $extra Extra options such as [sort] order.
     *
     * @return MicrobeResultSet
     */
    public static function all($param = null, $extra = null)
    {

        /** @var MicrobeModelMetadata $metadata */
        $metadata = static::microbe()->getModelMetadata(get_called_class());

        /** @var MicrobeModel $motherObject */
        $motherObject = $metadata->motherObject;

        if ($param) {
            if (is_array($param) === false) {
                $param = [
                    $metadata->pk => $metadata->connection->getPKValue($param),
                ];
            }
        }

        $cursor = $metadata->connection->getCursor(
            $metadata,
            $param,
            $extra,
            function ($r) use ($motherObject) {
                foreach ($r as $i => $state) {
                    yield $i => $motherObject->create($state, self::DRT_STABLE);
                }
            }
        );

        return new MicrobeResultSet(static::microbe()->getModelMetadata(get_called_class()), $cursor);
    }

    /**
     * Find one record (first or by condition).
     *
     * @param array $param Parameters of condition.
     * @param array $extra Extra options such as [sort] order.
     *
     * @return null|MicrobeModel
     */
    public static function one($param = null, $extra = null)
    {

        /** @var MicrobeModelMetadata $metadata */
        $metadata = static::microbe()->getModelMetadata(get_called_class());

        /** @var MicrobeModel $motherObject */
        $motherObject = $metadata->motherObject;

        if ($param) {
            if (is_array($param) === false) {
                foreach ($metadata->connection->_selPK($metadata, $param) as $state) {
                    return $motherObject->create($state, self::DRT_STABLE);
                }

                return null;
            }
        }

        foreach ($metadata->connection->_selChunk($metadata, $param) as $state) {
            return $motherObject->create($state, self::DRT_STABLE);
        }

        return null;
    }

    /**
     *
     */
    public static function oneUse($param = null, $extra = null)
    {
        if ($param instanceof MicrobeModel) {
            return $param;
        }

        return static::one($param, $extra);
    }

    /**
     *
     */
    public static function create($mixed = false, $dirty = self::DRT_TRANSIENT, $watchType = null)
    {
        if ($mixed === true) {
            if (is_array(static::$MicPreset)) {
                $mixed = static::$MicPreset;
            }
        }

        return new static($mixed, $dirty, $watchType);
    }

    /**
     *
     */
    public static function isEmpty($model)
    {
        return $model instanceof self ? $model->isNew() : empty($model);
    }

    /**
     *
     */
    public static function manage($class)
    {
        return static::microbe()->getModelManager()->loadStatic($class, static::whoIsMyNS());
    }

    /**
     *
     */
    public static function object($field = null)
    {
        return new MicrobeObject($field);
    }

    /**
     *
     */
    public static function key(& $v, $k, $d = null)
    {
        if (is_array($v)) {
            return isset($v[$k]) ? $v[$k] : $d;
        } else if (is_object($v)) {
            return isset($v->{$k}) ? $v->{$k} : $d;
        }

        return $v;
    }

    /**
     *
     */
    public static function keyEmpty(& $v, $k)
    {
        if (is_array($v)) {
            return empty($v[$k]);
        } else if (is_object($v)) {
            return empty($v->{$k});
        }

        return $v;
    }

    /**
     *
     */
    public static function keyIsset(& $v, $k)
    {
        if (is_array($v)) {
            return isset($v[$k]);
        } else if (is_object($v)) {
            return isset($v->{$k});
        }

        return $v;
    }

    /**
     *
     */
    public static function keyUnset(& $v, $k)
    {
        if (is_array($v)) {
            unset($v[$k]);
        } else if (is_object($v)) {
            unset($v->{ $k });
        }

        return $v;
    }

    /**
     *
     */
    public static function rem($param)
    {
        if ($param) {
            if (is_array($param) === false) {
                $param = [
                    static::getPK() => static::getConnection()->getPKValue($param),
                ];
            }
        }

        // TODO refactor, enhance productivity of related deletion if no external relations exist

        if (static::$MicIntegrity) {
            foreach (static::all($param) as $_model) {
                foreach (static::$MicRelations as $k => $relation) {
                    switch ($relation[0]) {
                        case MicrobeRelation::ONE:
                        case MicrobeRelation::ALL:
                            if (static::microbe()->getModelManager()->loadStatic($relation[3])->rem([
                                $relation[2] => $_model->getStateFieldPK()
                            ]) === false) {
                                return false;
                            }

                            break;
                    }
                }
            }
        }

        return static::getConnection()->_del(static::microbe()->getModelMetadata(get_called_class()), $param);
    }

    /**
     *
     */
    public function __construct($state = null, $dirty = self::DRT_TRANSIENT, $watchType = null)
    {
        if ($this->_metadata === null) {
            $this->_metadata = static::microbe()->getModelMetadata(get_called_class());
        }

        if ($state) {
            if ($state instanceof self) {
                $this->_state = $state->getState();
            } else
            if (is_array($state)) {
                $this->_state = $state;
            }
        }

        if ($dirty === self::DRT_TRANSIENT) {
            $this->_flags|= self::FLG_NEW;

            unset($this->_state[$this->_metadata->pk]);
        }

        $this->_dirty = $dirty;
    }

    /**
     *
     */
    public function __get($k)
    {
        if (isset($this->_metadata->relations[$k])) {
            return $this->getRelated($k);
        }

        return $this->getStateField($k);
    }

    /**
     *
     */
    public function __set($k, $v)
    {
        $this->unstabilize();

        if ($v instanceof MicrobeMetadata || isset($this->_metadata->relations[$k])) {

            /** @var MicrobeModel $v */
            $v = $this->setRelated($k, $v);

            if ($v) {
                $v->setRelated(static::whoAmI(), $this);
            }

            return null;
        }

        return $this->setStateField($k, $v);
    }

    /**
     *
     */
    public function __call($alias, $param = null)
    {
        return $this;
    }

    /**
     * Get dirty state:
     *
     * DRT_STABLE - linked and synchronized with DB;
     * DRT_TRANSIENT - not linked to DB (requested for insert);
     * DRT_LOCKED - is updating currently and locked for any save operation;
     * DRT_TRACKING - linked, but not synchronized with DB (requested for update);
     *
     * @return int Dirty state
     */
    public function getDirty()
    {
        return $this->_dirty;
    }

    /**
     * Get client extension for current adapter.
     *
     * This extension can be used for specific operation against data engine. Current model will be linked as data source.
     *
     * @return MicrobeClientExtension
     */
    public function getExtension()
    {
        return $this->_metadata->connectionWrite->getClientExtension($this);
    }

    /**
     * Get related model or set.
     *
     * @param string $key Alias of relation.
     * @param array $param Additional parameters.
     *
     * @return null|MicrobeCollection|MicrobeResultSet
     */
    public function getRelated($key, $param = null, $extra = null)
    {
        if (isset($this->_metadata->relations[$key])) {
            if (isset($this->_stateRelated[$key])) {
                return $this->_stateRelated[$key]->getR();
            }

            $relation = $this->_metadata->relations[$key];

            if (is_array($param) === false) {
                $param = [];
            }

            if (isset($param[$relation[2]]) === false) {
                $param[$relation[2]] = $this->getStateField($relation[1]);
            }

            switch ($relation[0]) {
                case MicrobeRelation::ONE:
                case MicrobeRelation::OWN:
                    $entity = $this->_metadata->getModelManager()->loadStatic($relation[3])->one($param, $extra);

                    if ($entity === null) {
                        return null;
                    }

                    $entity->{$relation[MicrobeRelation::IND_RELATION]} = $this;

                    $relation = new MicrobeRelation(
                        $this,
                        $entity,
                        $relation[0]
                    );

                    break;

                case MicrobeRelation::ALL:
                    $entity = $this->_metadata->getModelManager()->loadStatic($relation[3])->all($param, $extra)->toCollection();

                    if ($entity === null) {
                        return null;
                    }

                    $relation = new MicrobeRelation(
                        $this,
                        $entity,
                        $relation[0]
                    );

                    $entity->setRelationLeft($relation);

                    break;

                case MicrobeRelation::THROUGH:
                    return $this->run($relation[1]);

                default:
                    return null;
            }

            $this->_stateRelated[$key] = $relation;

            return $relation->getR();
        }

        return null;
    }

    /**
     *
     */
    public function&getState()
    {
        return $this->_state;
    }

    /**
     * Get raw field of state.
     *
     * @return mixed
     */
    public function getStateField($key, $defaultValue = null)
    {
        return isset($this->_state[$key]) ? $this->_state[$key] : $defaultValue;
    }

    /**
     * Get raw field of state.
     *
     * @return mixed
     */
    public function getStateFieldNotEmpty($key, $defaultValue = null)
    {
        return empty($this->_state[$key]) ? $defaultValue : $this->_state[$key];
    }

    /**
     * Get raw field of state PK-alias is mapped to.
     *
     * @return mixed
     */
    public function getStateFieldPK()
    {
        return $this->getStateField($this->_metadata->pk);
    }

    /**
     *
     */
    public function getStateFieldRelevant($key)
    {
        if (isset($this->_metadata->relations[$key])) {
            $relation = $this->_metadata->relations[$key];
            $entity = $this->getRelated($key);

            if ($entity instanceof self) {
                return $entity->getStateField($relation[2]);
            }
        }

        return null;
    }

    /**
     *
     */
    public function getValidator()
    {
        return $this->getMicrobe()->Validator;
    }

    /**
     * Set raw field of state.
     *
     * @return mixed
     */
    public function setStateField($key, $value)
    {
        $this->_state[$key] = $value;

        return $value;
    }

    /**
     * Get raw field of state PK-alias is mapped to.
     *
     * @return mixed
     */
    public function setStateFieldPK($value)
    {
        return $this->_state[$this->_metadata->pk] = $value;
    }

    /**
     * Set related model or set.
     *
     * @param string $key Alias of relation.
     * @param MicrobeState $value Instance of [MicrobeState] - base class of all data containers.
     *
     * @return null|MicrobeModel|MicrobeCollection|MicrobeResultSet
     *
     * @throws Exceptions\NotImplementedException
     * @throws MetadataIncompatibleException
     * @throws RelationIncompatibleEntityException
     * @throws RelationUnknownException
     */
    public function setRelated($key, MicrobeState $value)
    {
        if (isset($this->_metadata->relations[$key])) {
            $relation = $this->_metadata->relations[$key];

            switch ($relation[0]) {
                case MicrobeRelation::ONE:
                case MicrobeRelation::OWN:
                    if ($value instanceof MicrobeModel) {
                        $relation = new MicrobeRelation($this, $value, $relation[0]);
                    } else {
                        throw new RelationIncompatibleEntityException(
                            'On: ' . get_called_class() . ' -> ' . $key . ' ( ' . get_class($value) . ' )'
                        );
                    }

                    break;

                case MicrobeRelation::ALL:
                    if ($value instanceof MicrobeResultSet) {
                        if ($value->isMetadataCompatible($this->_metadata)) {
                            $relation = new MicrobeRelation(
                                $this,
                                $value,
                                MicrobeRelation::ALL,
                                $relation
                            );

                            $relation->getR()->setRelationLeft($relation);
                        } else {
                            throw new MetadataIncompatibleException(
                                'On: ' . get_called_class() . ' -> ' . $key . ' ( ' . get_class($value) . ' )'
                            );
                        }

                        $value = null;
                    } else {
                        if (isset($this->_stateRelated[$key]) === false) {
                            $relation = new MicrobeRelation(
                                $this,
                                new MicrobeCollection($this->_metadata),
                                MicrobeRelation::ALL,
                                $relation
                            );

                            $relation->getR()->setRelationLeft($relation);
                        } else {
                            $relation = $this->_stateRelated[$key];
                        }

                        $relation->getR()->add($value);
                    }

                    break;

                default:
                    throw new RelationUnknownException('On: ' . get_called_class() . ' -> ' . $key);
            }

            $this->_stateRelated[$key] = $relation;

            return $value;
        }

        return null;
    }

    /**
     * @return $this
     */
    public function assign($state)
    {
        if ($state instanceof self) {
            $state = $state->getState();
        }

        if (is_array($state)) {
            foreach ($state as $k => $v) {
                $this->__set($k, $v);
            }
        }

        return $this;
    }

    /**
     *
     */
    public function del($extra = null)
    {
        if (static::rem($this->getStateFieldPK())) {
            $this->_dirty = self::DRT_TRANSIENT;

            $this->setStateFieldPK(null);

            return true;
        };

        return false;
    }

    /**
     *
     */
    public function delRelated($extra = null)
    {
        if (static::rem($this->getStateFieldPK())) {
            $this->_dirty = self::DRT_TRANSIENT;

            $this->setStateFieldPK(null);

            return true;
        };

        return false;
    }

    /**
     *
     */
    public function initialize()
    {

    }

    /**
     *
     */
    public function isNew()
    {
        return $this->_dirty === self::DRT_TRANSIENT;
    }

    /**
     *
     */
    public function isNewCreated()
    {
        return $this->getFlag(self::FLG_NEW, true) === self::FLG_NEW;
    }

    /**
     *
     */
    public function isStable()
    {
        return $this->_dirty === self::DRT_STABLE;
    }

    /**
     *
     */
    public function put($extra = null)
    {
        if ($this->_dirty === self::DRT_LOCKED) {
            return self::DRT_LOCKED;
        }

        // save current dirty state
        $dirty = $this->_dirty;

        // set dirty state to locked to prevent "loop" updates (A->B->A->B->...)
        $this->_dirty = self::DRT_LOCKED;

        // put owners
        foreach ($this->_stateRelated as $k => $state) {

            /** @var MicrobeRelation $state */
            if ($state->getType() === MicrobeRelation::OWN) {
                $relation = $this->_metadata->relations[$k];

                $state->getR()->put(true);
                $state->getL()->setStateField($relation[1], $state->getR()->getStateField($relation[2]));
            }
        }

        switch ($dirty) {

            // for insert
            case self::DRT_TRANSIENT:
                $result = $this->_metadata->connectionWrite->_ins(
                    $this->_metadata,
                    $this->_state
                );

                if ($result) {
                    $this->_state[$this->_metadata->pk] = $result;
                }

                break;

            // for update
            case self::DRT_TRACKING:
                $result = $this->_metadata->connectionWrite->_updPartialPK(
                    $this->_metadata,
                    $this->_state
                );

                if ($result) {

                }

                break;

            default:
                $result = 1;
        }

        // if error restore last dirty state
        if (empty($result)) {
            $this->_dirty = $dirty;

            return $this->_dirty === self::DRT_STABLE;
        }

        $this->_stateChanged = [];

        // put owned (related by [ONE] or [ALL])
        foreach ($this->_stateRelated as $k => $state) {

            /** @var MicrobeRelation $state */
            switch ($state->getType()) {
                case MicrobeRelation::ONE:
                    $relation = $this->_metadata->relations[$k];

                    $state->getR()->setStateField($relation[2], $state->getL()->getStateField($relation[1]));
                    $state->getR()->put(true);

                    break;

                case MicrobeRelation::ALL:
                    $state->getR()->put();

                    break;
            }
        }

        $this->_dirty = self::DRT_STABLE;

        return true;
    }

    /**
     *
     */
    public function putPartial($field)
    {
        if ($this->isNew() === false) {
            $fields = [];

            foreach ($field as $k => $v) {
                if (is_numeric($k)) {
                    $fields[$v] = $this->getStateField($v);
                } else {
                    $fields[$k] = $this->setStateField($k, $v);
                }
            }

            return $this->_metadata->connectionWrite->_updPartial($this->_metadata, $fields);
        }

        return false;
    }

    /**
     *
     */
    public function run($k)
    {
        return $this->__get($k);
    }

    /**
     *
     */
    public function toArray()
    {
        $result = $this->_state;

        foreach ($this->_stateRelated as $i => $state) {

            /** @var MicrobeRelation $state */
            switch ($state->getType()) {
                case MicrobeRelation::ALL:
                case MicrobeRelation::ONE:
                    $result[$i] = $state->getR()->toArray();
            }
        }

        return $result;
    }

    /**
     * @return $this
     */
    public function unstabilize()
    {
        if ($this->_dirty !== self::DRT_TRANSIENT) {
            $this->_dirty = self::DRT_TRACKING;
        }

        return $this;
    }

    /**
     *
     */
    public function validate($index, $filter = true)
    {
        $this->getValidator()->setExternal($this)->setFilter($filter);

        if (isset(static::$MicValidators[$index])) {
            return $this->getValidator()->validate($this->_state, static::$MicValidators[$index]);
        }

        return false;
    }

}

<?php

namespace Microbe;

use Microbe\Exceptions\Adapter\AdapterNotExistsException;

/**
 * Class MicrobeConnection
 * @package Microbe
 */
class MicrobeConnection extends MicrobeBase implements \ArrayAccess
{

    /** @var MicrobeAdapter $_adapter */
    protected $_adapter;

    /** @var string $alias */
    public $alias;

    /** @var string $host */
    public $host;

    /** @var string $name */
    public $name;

    /** @var array|null $options */
    public $options;

    /** @var string $pass */
    public $pass = 'test';

    /** @var string $type */
    public $type;

    /** @var string $user */
    public $user = 'test';

    /**
     *
     */
    protected function onAdapterCreate()
    {
        $connectionAdapter = __NAMESPACE__ . '\\Adapters\\Adapter' . $this->type;

        if (class_exists($connectionAdapter) === false) {
            throw new AdapterNotExistsException('On connection: ' . $this->alias);
        }

        return new $connectionAdapter($this->_microbe, $this);
    }

    /**
     *
     */
    public static function create(Microbe $microbe, $credentials = null)
    {
        return new static($microbe, $credentials);
    }

    /**
     *
     */
    public function __construct(Microbe $microbe, $credentials = null)
    {
        $this->_microbe = $microbe;

        $this->configure($credentials, false);
    }

    /**
     *
     */
    public function offsetGet($k)
    {
        return isset($this->{$k}) ? $this->{$k} : null;
    }

    /**
     *
     */
    public function offsetSet($k, $v)
    {
        $this->{$k} = $v;
    }

    /**
     *
     */
    public function offsetExists($k)
    {
        return isset($this->{$k});
    }

    /**
     *
     */
    public function offsetUnset($k)
    {

    }

    /**
     * @return MicrobeAdapter
     */
    public function getAdapter($force = false)
    {
        return $this->_adapter === null || $force
            ? $this->_adapter = $this->onAdapterCreate()
            : $this->_adapter;
    }

    /**
     *
     */
    public function setAdapter(MicrobeAdapter $value)
    {
        $this->_adapter = $value;

        return $this;
    }

    /**
     *
     */
    public function configure($credentials, $forceRefresh = true)
    {
        if (is_array($credentials) || $credentials instanceof \Traversable) {
            foreach ($credentials as $k => $v) {
                $this->{$k} = $v;
            }
        }

        $forceRefresh ? $this->getAdapter()->connectionRefresh() : null;

        return $this;
    }

}

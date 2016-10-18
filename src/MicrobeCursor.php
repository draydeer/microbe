<?php

namespace Microbe;

use Microbe\Exceptions\Exception;
use Microbe\Exceptions\RequestException;

/**
 * Class MicrobeCursor
 * @package Microbe
 */
abstract class MicrobeCursor extends MicrobeMetadata implements \IteratorAggregate
{

    const MOD_CONTINUOUS = 9999999999;
    
    /** @var int $_index */
    protected $_index = 0;

    /** @var mixed $_extra */
    protected $_extra;

    /** @var callable $_fetcherFunc */
    protected $_fetcherFunc;

    /** @var int $_fetchLimit */
    protected $_fetchLimit = self::MOD_CONTINUOUS;

    /** @var mixed $_param */
    protected $_param;

    /**
     *
     */
    public static function getFetchable($mixed, $timeout = 0)
    {
        return $mixed;
    }

    /**
     *
     */
    public function __construct(
        MicrobeModelMetadata $metadata,
        $param = null,
        $extra = null,
        callable $fetcherFunc = null
    )
    {
        $this->_extra = $extra;
        $this->_fetcherFunc = $fetcherFunc;
        $this->_metadata = $metadata;
        $this->_param = $param;
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        $method = $this->_fetcherFunc;

        $cursor = function() use ($method) {
            $limit = $this->getQueryExtra('l', 0);
            $start = $startOffset = $this->getQueryExtra('o', 0);

            do {
                $break = true;
                $empty = true;

                try {
                    foreach (static::getFetchable(
                        $this->_metadata->connection->_sel(
                            $this->_metadata,
                            $this->_param,
                            $limit > 0 || $this->_fetchLimit !== self::MOD_CONTINUOUS
                                ? [
                                    'limit' => $this->_fetchLimit === self::MOD_CONTINUOUS
                                        ? $limit - $start + $startOffset
                                        : $this->_fetchLimit,
                                    'offset' => $start,
                                ]
                                : [
                                    'offset' => $start
                                ],
                            false,
                            true
                        ),
                        $this->_metadata->connection->getCursorTimeout()
                    ) as $v) {
                        yield $start - $startOffset => $v;

                        $start ++;

                        if ($limit > 0 && $start - $startOffset >= $limit) {
                            break 2;
                        } else {
                            $break = $this->_fetchLimit === self::MOD_CONTINUOUS;
                        }

                        $empty = false;
                    }
                } catch (\Exception $e) {
                    if ($e instanceof Exception) {
                        $e = $e->getPrevious();
                    }

                    if ($this->_metadata->connection->processExceptionOnRequest($e) === false) {
                        throw RequestException::createFromException($e);
                    } else {
                        $break = false;
                    }
                }
            } while ($break === false && $empty === false);
        };

        return $method ? $method($cursor()) : $cursor();
    }

    /**
     *
     */
    public function getAggAverage()
    {
        return $this->_metadata->connection->_aggAverage($this->_metadata, $this->_param);
    }

    /**
     *
     */
    public function getAggCount()
    {
        return $this->_metadata->connection->_aggCount($this->_metadata, $this->_param, $this->_extra);
    }

    /**
     *
     */
    public function getAggMin()
    {
        return $this->_metadata->connection->_aggMin($this->_metadata, $this->_param);
    }

    /**
     *
     */
    public function getAggMax()
    {
        return $this->_metadata->connection->_aggMax($this->_metadata, $this->_param);
    }

    /**
     *
     */
    public function getAggSum()
    {
        return $this->_metadata->connection->_aggSum($this->_metadata, $this->_param);
    }

    /**
     * Fetcher paginated and limited to [_fetchLimit] records in single query.
     *
     * Useful is case of unstable connection when cursor can be closed remotely.
     */
    public function setChunked($limit = self::MOD_CONTINUOUS)
    {
        $this->_fetchLimit = $limit >= 0 ? $limit : self::MOD_CONTINUOUS;

        return $this;
    }

    /**
     *
     */
    public function getQueryExtra($k = null, $default = null)
    {
        if (is_array($this->_extra) === false) {
            $this->_extra = [];
        }

        return $k ? (isset($this->_extra[$k]) ? $this->_extra[$k] : $default) : $this->_extra;
    }

    /**
     *
     */
    public function getQueryParam($k = null, $default = null)
    {
        if (is_array($this->_param) === false) {
            $this->_param = [];
        }

        return $k ? (isset($this->_param[$k]) ? $this->_param[$k] : $default) : $this->_param;
    }

    /**
     *
     */
    public function queryExtraMerge(array $value)
    {
        $this->_extra = array_merge($this->getQueryExtra(), $value);

        return $this;
    }

    /**
     *
     */
    public function queryParamMerge(array $value)
    {
        $this->_param = array_merge($this->getQueryParam(), $value);

        return $this;
    }

}

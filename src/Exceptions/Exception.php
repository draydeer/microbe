<?php

namespace Microbe\Exceptions;
use Microbe\Traits\Ext\GetInstanceTrait;

/**
 * Class Exception
 * @package Microbe\Exceptions
 */
class Exception extends \Exception
{
    use GetInstanceTrait;

    /**
     * return static
     */
    public static function create($message, $code = 0, \Exception $previous = null)
    {
        return $previous ? new static($message, $code, $previous) : new static($message, $code);
    }

    /**
     * return static
     */
    public static function createFromException(\Exception $previous)
    {
        return $previous instanceof Exception
            ? $previous
            : new static(
                is_string($previous->getMessage()) ? $previous->getMessage() : '?',
                is_long($previous->getCode()) ? $previous->getCode() : 0,
                $previous
            );
    }

}

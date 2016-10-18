<?php

namespace Microbe\Services;

use Microbe\Exceptions\Validator\ValidatorNotExistsException;
use Microbe\MicrobeService;

/**
 * Class ServiceValidator
 * @package Microbe\Services
 */
class ServiceValidator extends MicrobeService
{

    /** @var object $external */
    protected $external;

    /** @var bool $filter */
    protected $filter = true;

    /**
     *
     */
    public static function getValue($k, & $v, $valueDefault = null)
    {
        return is_array($v) ? (isset($v[$k]) ? $v[$k] : $valueDefault) : (is_object($v) ? (isset($v->{ $k }) ? $v->{ $k } : $valueDefault) : null);
    }

    /**
     *
     */
    public static function setValue($k, & $v, $value = null)
    {
        return is_array($v) ? $v[$k] = $value : (is_object($v) ? $v->{ $k } = $value : null);
    }

    /**
     *
     */
    public static function getValueOrSet($k, & $v, $valueDefault = null)
    {
        return is_array($v) ? (isset($v[$k]) ? $v[$k] : $v[$k] = $valueDefault) : (is_object($v) ? (isset($v->{ $k }) ? $v->{ $k } : $v->{ $k } = $valueDefault) : null);
    }

    /**
     *
     */
    public function setExternal($value)
    {
        $this->external = $value;

        return $this;
    }

    /**
     *
     */
    public function setFilter($value = true)
    {
        $this->filter = $value;

        return $this;
    }

    /**
     *
     */
    public static function pick($value, array $fields)
    {
        if (is_array($value)) {
            $result = [];

            foreach ($fields as $f) {
                if (isset($value[$f])) {
                    $result[$f] = $value[$f];
                }
            }

            return $result;
        }

        if (is_object($value)) {
            $result = new \stdClass();

            foreach ($fields as $f) {
                if (isset($value->{ $f })) {
                    $result->{ $f } = $value->{ $f };
                }
            }

            return $result;
        }

        return null;
    }

    /**
     *
     */
    public static function asArr($val, $def = null)
    {
        return is_array($val) ? $val : (array) $def;
    }

    /**
     *
     */
    public static function asArrKey($val, $key, $def = null)
    {
        return isset($val[$key]) ? $val[$key] : $def;
    }

    /**
     *
     */
    public static function asBoolean($val, $custom = null)
    {
        return $val == '1' || $val === 'true' || ($custom !== null && $val === $custom);
    }

    /**
     *
     */
    public static function asDouble($val)
    {
        return is_numeric($val) ? doubleval($val) : 0;
    }

    /**
     *
     */
    public static function asDoublePositive($val)
    {
        return is_numeric($val) ? abs(doubleval($val)) : 0;
    }

    /**
     *
     */
    public static function asDoubleRange($val, $min, $max)
    {
        return is_numeric($val) ? doubleval($val < $min ? $min : ($val > $max ? $max : $val)) : $min;
    }

    /**
     *
     */
    public static function asFloat($val)
    {
        return is_numeric($val) ? floatval($val) : 0;
    }

    /**
     *
     */
    public static function asFloatPositive($val)
    {
        return is_numeric($val) ? abs(floatval($val)) : 0;
    }

    /**
     *
     */
    public static function asFloatRange($val, $min, $max)
    {
        return is_numeric($val) ? floatval($val < $min ? $min : ($val > $max ? $max : $val)) : $min;
    }

    /**
     *
     */
    public static function asInt($val)
    {
        return is_numeric($val) ? intval($val) : 0;
    }

    /**
     *
     */
    public static function asIntPositive($val)
    {
        return is_numeric($val) ? abs(intval($val)) : 0;
    }

    /**
     *
     */
    public static function asIntRange($val, $min, $max)
    {
        return is_numeric($val) ? intval($val < $min ? $min : ($val > $max ? $max : $val)) : $min;
    }

    /**
     *
     */
    public static function asObj($val, $def = null)
    {
        return is_object($val) ? $val : (object) $def;
    }

    /**
     *
     */
    public static function asObjKey($val, $key, $def = null)
    {
        return isset($val->{ $key }) ? $val->{ $key } : $def;
    }

    /**
     *
     */
    public static function asString($val)
    {
        return is_array($val) ? '' : (string) $val;
    }

    /**
     *
     */
    public static function asEmail($val)
    {
        return filter_var($val, FILTER_SANITIZE_EMAIL);
    }

    /**
     *
     */
    public static function asEncoded($val)
    {
        return filter_var($val, FILTER_SANITIZE_ENCODED);
    }

    /**
     *
     */
    public static function asSpecialChars($val)
    {
        return filter_var($val, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     *
     */
    public static function asUrl($val)
    {
        return filter_var($val, FILTER_SANITIZE_URL);
    }

    /**
     *
     */
    public static function isArr($val)
    {
        return is_array($val);
    }

    /**
     *
     */
    public static function isArrKey($val, $key)
    {
        return isset($val[$key]);
    }

    /**
     *
     */
    public static function isBoolean($val)
    {
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     *
     */
    public static function isDateTimeParsable($val)
    {
        return strtotime($val) !== 0;
    }

    /**
     *
     */
    public static function isEmail($val)
    {
        return filter_var($val, FILTER_VALIDATE_EMAIL);
    }

    /**
     *
     */
    public static function isEmpty($val)
    {
        return empty($val);
    }

    /**
     *
     */
    public static function isEmptyContainer($val)
    {
        return (is_array($val) && empty($val)) || (is_object($val) && empty(get_object_vars($val)));
    }

    /**
     *
     */
    public static function isFloat($val)
    {
        return filter_var($val, FILTER_VALIDATE_FLOAT);
    }

    /**
     *
     */
    public static function isIn($val, $in, $strict = true)
    {
        return in_array($val, $in, $strict);
    }

    /**
     *
     */
    public static function isInt($val)
    {
        return filter_var($val, FILTER_VALIDATE_INT);
    }

    /**
     *
     */
    public static function isIP($val)
    {
        return filter_var($val, FILTER_VALIDATE_IP);
    }

    /**
     *
     */
    public static function isNotEmpty($val)
    {
        return ! static::isEmpty($val);
    }

    /**
     *
     */
    public static function isNotEmptyContainer($val)
    {
        return ! static::isEmptyContainer($val);
    }

    /**
     *
     */
    public static function isNull($val)
    {
        return is_null($val);
    }

    /**
     *
     */
    public static function isObj($val)
    {
        return is_object($val);
    }

    /**
     *
     */
    public static function isObjKey($val, $key)
    {
        return isset($val->{ $key });
    }

    /**
     *
     */
    public static function isRegex($val, $rgx)
    {
        return filter_var($val, FILTER_VALIDATE_REGEXP, ['options'=>['regexp' => $rgx]]);
    }

    /**
     *
     */
    public static function isString($val)
    {
        return is_string($val);
    }

    /**
     *
     */
    public static function isEq($val, $cmp)
    {
        return $val === $cmp;
    }

    /**
     *
     */
    public static function isGe($val, $cmp)
    {
        return is_numeric($val) && $val >= $cmp;
    }

    /**
     *
     */
    public static function isGt($val, $cmp)
    {
        return is_numeric($val) && $val > $cmp;
    }

    /**
     *
     */
    public static function isLe($val, $cmp)
    {
        return is_numeric($val) && $val <= $cmp;
    }

    /**
     *
     */
    public static function isLt($val, $cmp)
    {
        return is_numeric($val) && $val < $cmp;
    }

    /**
     *
     */
    public static function isNe($val, $cmp)
    {
        return $val !== $cmp;
    }

    /**
     *
     */
    public function validate(& $value, array $scenario, $forceAll = true, array & $result = [])
    {
        if (is_array($value) === false && is_object($value) === false) {
            return false;
        }

        $filter = $this->filter;

        $this->filter = true;

        // custom validator before general scenario
        if ($this->external && isset($scenario['#']) && method_exists($this->external, $scenario['#'])) {
            if ($this->external->{ $scenario['#'] }($value) !== true) {
                return $result;
            }
        }

        foreach ($value as $k =>&$v) {
            if (isset($scenario[$k]) === false) {
                if (is_array($value)) {
                    if ($filter) {
                        unset($value[$k]);
                    }

                    continue;
                }

                if (is_object($value)) {
                    if ($filter) {
                        unset($value->{ $k });
                    }

                    continue;
                }
            }

            $_scenario = $scenario[$k];

            if (is_array($_scenario) === false) {
                $_scenario = [ $_scenario ];
            }

            foreach ($_scenario as $_method => $_params) {
                if (is_numeric($_method)) {
                    $_method = $_params;
                    $_params = [];
                }

                // inner validator
                if (is_array($_method)) {
                    if ($this->validate($v, $_method, $forceAll, $result) !== true) {
                        return $result;
                    } else {
                        continue;
                    }
                }

                if (method_exists($this, $_method)) {

                    // [as] - [is]
                    if ($_method[0] === 'a') {
                        $set = static::$_method($v, static::getValue(0, $_params), static::getValue(1, $_params), static::getValue(2, $_params), static::getValue(3, $_params));

                        if (is_array($value)) {
                            $value[$k] = $set;
                        } else if (is_object($value)) {
                            $value->{ $k } = $set;
                        }
                    } else {
                        if (static::$_method($v, static::getValue(0, $_params), static::getValue(1, $_params), static::getValue(2, $_params), static::getValue(3, $_params)) === false) {
                            $result[$k] = false;

                            if ($forceAll === false) {
                                return $result;
                            }
                        }
                    }
                }
            }
        }

        if (is_array($value)) {
            foreach ($scenario as $k => $_scenario) {
                if (is_array($_scenario)) {
                    if (isset($_scenario['default']) && isset($value[$k]) === false) {
                        $value[$k] = $_scenario['default'];

                        continue;
                    }

                    if (isset($_scenario['isExists']) && isset($value[$k]) === false) {
                        $result[$k] = false;

                        return $result;
                    }
                }
            }
        } else if (is_object($value)) {
            foreach ($scenario as $k => $_scenario) {
                if (is_array($_scenario)) {
                    if (isset($_scenario['default']) && isset($value->{ $k }) === false) {
                        $value->{ $k } = $_scenario['default'];

                        continue;
                    }

                    if (isset($_scenario['isExists']) && isset($value->{ $k }) === false) {
                        $result[$k] = false;

                        return $result;
                    }
                }
            }
        }

        return empty($result) ? true : $result;
    }

}

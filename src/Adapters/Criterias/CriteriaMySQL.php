<?php

namespace Microbe\Adapters\Criterias;

use Microbe\Adapters\Dialects\Traits\TraitDialectMySQL;
use Microbe\MicrobeCriteria;

/**
 * Class CriteriaMySQL
 * @package Microbe\Adapters\Criterias
 */
class CriteriaMySQL extends MicrobeCriteria
{
    use TraitDialectMySQL;

    const
        VAL_EQ          = '=',
        VAL_GE          = '>=';

    /**
     *
     */
    public static function compileParametrized(
        $alias,
      & $param,
        $forceQuery = false,
        $pk = null,
      & $paramBind = []
    )
    {
        $_querySimple = '';

        if (is_array($param)) {
            $_dialectEOL = static::getDialectForEOL();

            $_query = '';

            // block of bind values
            if (isset($param['#'])) {
                $paramBind = $param['#'];

                unset($param['#']);
            }

            foreach ($param as $I => $V) {
                if (is_array($V)) {
                    $_query.= '(' . static::compileParametrized(
                        $alias,
                        $V,
                        true,
                        $pk,
                        $paramBind
                    ) . ') OR ';
                } else {
                    if (is_numeric($I)) {
                        $_querySimple.= '(' . $V . ')';

                        unset($param[$I]);
                    } else {
                        $_querySimple.= $alias . '.' . $I . '=:' . $I;

                        $paramBind[$I] = $V;
                    }

                    $_querySimple.= ' AND ';
                }
            }

            if (empty($_query) === false) {
                $_querySimple = (empty($_querySimple) ? null : '(' . $_querySimple . $_dialectEOL . ') OR ') . substr($_query, 0, - 4);
            } else {
                $_querySimple.= $_dialectEOL;
            }
        } else {
            $_querySimple = $alias . '.' . $pk . '=:_pk';

            $paramBind['_pk'] = $param;
        }

        if ($forceQuery) {
            return $_querySimple;
        }

        return [
            $_querySimple,
            $paramBind
        ];
    }

    /**
     *
     */
    protected function getRight(
        $alias,
        $aliasBind
    )
    {
        if ($alias === null) {
            return ':' . $aliasBind;
        }

        return $this->R . '.' . $alias;
    }

    /**
     *
     */
    public function _eq(
        $L,
        $R = null
    )
    {
        $this->CriteriaReference[] = $this->L . '.' . $L . self::VAL_EQ . $this->getRight($R, $L);

        return $this;
    }

    /**
     *
     */
    public function _ge(
        $L,
        $R = null
    )
    {
        $this->CriteriaReference[] = $this->L . '.' . $L . self::VAL_GE . $this->getRight($R, $L);

        return $this;
    }

    /**
     *
     */
    public function _gt(
        $L,
        $R = null
    )
    {

    }

    /**
     *
     */
    public function _le(
        $L,
        $R = null
    )
    {

    }

    /**
     *
     */
    public function _lt(
        $L,
        $R = null
    )
    {

    }

    /**
     *
     */
    public function _ne(
        $L,
        $R = null
    )
    {

    }
}

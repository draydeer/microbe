<?php

namespace Microbe\Adapters\Criterias;

use Microbe\Adapters\Dialects\Traits\TraitDialectPostgreSQL;
use Microbe\MicrobeCriteria;

/**
 * Class CriteriaMySQL
 * @package Microbe\Adapters\Criterias
 */
class CriteriaPostgreSQL extends CriteriaMySQL
{
    use TraitDialectPostgreSQL;
}

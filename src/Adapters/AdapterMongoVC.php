<?php

namespace Microbe\Adapters;

use Microbe\Adapters\ClientsExtensions\ClientExtensionMongo;
use Microbe\Adapters\Criterias\CriteriaMongo;
use Microbe\Exceptions\Adapter\AdapterIncompatibleConditionException;
use Microbe\Exceptions\NotImplementedException;
use Microbe\Exceptions\NotImplementedAdapterNativeClientException;
use Microbe\Exceptions\RequestException;
use Microbe\MicrobeAdapter;
use Microbe\MicrobeModelMetadata;
use Microbe\MicrobeModel;

/**
 * Class AdapterMongo
 * @package Microbe\Adapters
 */
class AdapterMongoVC extends AdapterMongo
{

    /**
     *
     */
    public function _ins(
        MicrobeModelMetadata $metadata,
        $value,
        $extra = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {
                if (isset($value['_vc']) === false) {
                    $value['_vc'] = 0;
                }

                $_result = $this->_dbTarget->{ $metadata->schema }->insert($value);

                return $_result ? $value[$metadata->pk] : $_result;
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }

    /**
     *
     */
    public function _updPK(
        MicrobeModelMetadata $metadata,
        $value,
        $param = null,
        $watchType = self::TYP_WATCH_ORIGIN,
        $forceThrow = false
    )
    {
        if ($this->_connected === false) {
            $this->onInitConnection();
        }

        while (1) {
            try {
                if (isset($value['_vc'])) {
                    $value['_vc'] ++;

                    return $this->_dbTarget->{$metadata->schema}->update(
                        [
                            $metadata->pk => $value[$metadata->pk],
                            '_vc' => $value['_vc'] - 1,
                        ],
                        $value
                    );
                } else {
                    $value['_vc'] = 0;

                    return $this->_dbTarget->{$metadata->schema}->update(
                        [
                            $metadata->pk => $value[$metadata->pk],
                            '_vc' => null,
                        ],
                        $value
                    );
                }
            } catch (\Exception $e) {
                if ($forceThrow || $this->processExceptionOnRequest($e) === false) {
                    throw RequestException::createFromException($e);
                }
            }
        }
    }
}

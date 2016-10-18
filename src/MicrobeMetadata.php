<?php

namespace Microbe;

/**
 * Class MicrobeMetadata
 * @package Microbe
 */
class MicrobeMetadata extends MicrobeBase
{

    /** @var string */
    protected static $_microbeInjected = null;

    /** @var MicrobeModelMetadata $Metadata */
    protected $_metadata;

    /**
     * Get [Microbe] instance.
     *
     * @return Microbe
     */
    public static function microbe()
    {
        if (is_string(static::$_microbeInjected)) {
            /*
            if ($microbe = Microbe::getShared(static::$_microbeInjected)) {
                return $microbe;
            }

            throw new NotFoundException('On: ' . $injected);
            */
        }

        return Microbe::getInstanceShared();
    }

    /**
     * Get [MicrobeModelMetadata] instance.
     *
     * @return MicrobeModelMetadata
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * Get [Microbe] instance.
     *
     * @return Microbe
     */
    public function getMicrobe()
    {
        return $this->_metadata->microbe;
    }

    /**
     *
     */
    public function isMetadataCompatible(MicrobeModelMetadata $metadata)
    {
        return true;
    }

}

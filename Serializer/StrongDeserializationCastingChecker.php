<?php

namespace Smartbox\CoreBundle\Serializer;

/**
 * Class StrongDeserializationCastingChecker
 * @package Smartbox\CoreBundle\Serializer
 */
class StrongDeserializationCastingChecker implements DeserializationCastingChecker
{
    /**
     * {@inheritDoc}
     */
    public function canBeCastedToString($data)
    {
        return is_string($data) || is_null($data);
    }

    /**
     * {@inheritDoc}
     */
    public function canBeCastedToBoolean($data)
    {
        return is_bool($data) ||
            is_null($data) ||
            $data === 'true' ||
            $data === 'false' ||
            $data === 1 ||
            $data === 0
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function canBeCastedToInteger($data)
    {
        return is_null($data) ||
            (is_numeric($data) && floor($data) == $data);
    }

    /**
     * {@inheritDoc}
     */
    public function canBeCastedToDouble($data)
    {
        return is_numeric($data) || is_null($data);
    }
}
<?php

namespace Smartbox\CoreBundle\Serializer;

/**
 * Class StrongDeserializationCastingChecker.
 */
class StrongDeserializationCastingChecker implements DeserializationCastingCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canBeCastedToString($data)
    {
        return !is_object($data) && !is_array($data);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeCastedToBoolean($data)
    {
        return is_bool($data) ||
            is_null($data) ||
            $data === 'true' ||
            $data === 'false' ||
            $data === 1 || $data === '1' ||
            $data === 0 || $data === '0'
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeCastedToInteger($data)
    {
        return is_null($data) ||
            (is_numeric($data) && floor($data) == $data);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeCastedToDouble($data)
    {
        return is_numeric($data) || is_null($data);
    }
}

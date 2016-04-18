<?php

namespace Smartbox\CoreBundle\Serializer;

/**
 * Interface DeserializationCastingCheckerInterface.
 */
interface DeserializationCastingCheckerInterface
{
    /**
     * Checks if the given data can be safely casted to string.
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function canBeCastedToString($data);

    /**
     * Checks if the given data can be safely casted to boolean.
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function canBeCastedToBoolean($data);

    /**
     * Checks if the given data can be safely casted to integer.
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function canBeCastedToInteger($data);

    /**
     * Checks if the given data can be safely casted to double.
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function canBeCastedToDouble($data);
}

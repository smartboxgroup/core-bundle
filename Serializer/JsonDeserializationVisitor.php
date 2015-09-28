<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

/**
 * Class JsonDeserializationVisitor
 * @package Smartbox\CoreBundle\Serializer
 */
class JsonDeserializationVisitor extends \JMS\Serializer\JsonDeserializationVisitor
{
    use CastingCheckerAwareVisitor;

    /**
     * Constructor
     *
     * @param PropertyNamingStrategyInterface $namingStrategy
     * @param ObjectConstructorInterface $objectConstructor
     * @param DeserializationCastingChecker $castingChecker
     */
    public function __construct(PropertyNamingStrategyInterface $namingStrategy,
                                ObjectConstructorInterface $objectConstructor,
                                DeserializationCastingChecker $castingChecker)
    {
        parent::__construct($namingStrategy, $objectConstructor);
        $this->castingChecker = $castingChecker;
    }
}

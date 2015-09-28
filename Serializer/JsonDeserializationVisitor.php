<?php

namespace Smartbox\CoreBundle\Serializer;

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
     * @param DeserializationCastingChecker $castingChecker
     */
    public function __construct(PropertyNamingStrategyInterface $namingStrategy,
                                DeserializationCastingChecker $castingChecker)
    {
        parent::__construct($namingStrategy);
        $this->castingChecker = $castingChecker;
    }
}

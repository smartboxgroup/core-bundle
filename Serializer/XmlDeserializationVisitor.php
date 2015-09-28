<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

/**
 * Class XmlDeserializationVisitor
 * @package Smartbox\CoreBundle\Serializer
 */
class XmlDeserializationVisitor extends \JMS\Serializer\XmlDeserializationVisitor
{
    use CastingCheckerVisitor;

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

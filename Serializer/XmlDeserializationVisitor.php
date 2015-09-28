<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

/**
 * Class XmlDeserializationVisitor
 * @package Smartbox\CoreBundle\Serializer
 */
class XmlDeserializationVisitor extends \JMS\Serializer\XmlDeserializationVisitor
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

<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Smartbox\CoreBundle\Serializer\CastingCheckerVisitor;
use Smartbox\CoreBundle\Serializer\DeserializationCastingChecker;

class CastingCheckerDeserializer extends FakeSerializerVisitor
{
    use CastingCheckerVisitor;

    /** @var PropertyNamingStrategyInterface */
    private $namingStrategy;

    /**
     * Constructor
     *
     * @param PropertyNamingStrategyInterface $namingStrategy
     * @param DeserializationCastingChecker $castingChecker
     */
    public function __construct(PropertyNamingStrategyInterface $namingStrategy,
                                DeserializationCastingChecker $castingChecker)
    {
        $this->namingStrategy = $namingStrategy;
        $this->castingChecker = $castingChecker;
    }

    public function getCurrentObject()
    {
        $obj = new \stdClass();
        $obj->property = 'some value';

        return $obj;
    }
}
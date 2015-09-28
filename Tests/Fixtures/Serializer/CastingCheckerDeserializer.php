<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Smartbox\CoreBundle\Serializer\CastingCheckerAwareVisitor;
use Smartbox\CoreBundle\Serializer\DeserializationCastingChecker;

class DummyParent
{
    public function visitString($data, array $type, Context $context) {}
    public function visitBoolean($data, array $type, Context $context) {}
    public function visitInteger($data, array $type, Context $context) {}
    public function visitDouble($data, array $type, Context $context) {}
    public function visitArray($data, array $type, Context $context) {}
}

class CastingCheckerDeserializer extends DummyParent
{
    use CastingCheckerAwareVisitor;

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
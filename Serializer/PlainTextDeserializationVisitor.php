<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\AbstractVisitor;
use JMS\Serializer\Context;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * This is a really simple visitor that will effectively bypass JMS. Useful for REST APIs that are not truly REST APIs
 * and return plain text responses, which JMS by default refuses to process because they are not real JSON.
 */
class PlainTextDeserializationVisitor extends AbstractVisitor
{
    public function visitNull($data, array $type, Context $context)
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Null types.');
    }

    public function visitString($data, array $type, Context $context)
    {
        return $data;
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Boolean types.');
    }

    public function visitDouble($data, array $type, Context $context)
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Double types.');
    }

    public function visitInteger($data, array $type, Context $context)
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Integer types.');
    }

    public function visitArray($data, array $type, Context $context)
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Array types.');
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        // noop
    }

    public function visitProperty(PropertyMetadata $metadata, $data, Context $context)
    {
        // noop
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        // noop
    }

    public function setNavigator(GraphNavigator $navigator)
    {
        // noop
    }

    public function getNavigator()
    {
        // noop
    }

    public function getResult()
    {
        return null;
    }
}

<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\AbstractVisitor;
use JMS\Serializer\Context;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * This is a really simple visitor that will effectively bypass JMS.
 * Useful for cases when JMS receives a plain text to deserialize.
 */
class PlainTextDeserializationVisitor extends AbstractVisitor
{
    public function visitString($data, array $type, Context $context): string
    {
        return (string) $data;
    }

    public function visitBoolean($data, array $type, Context $context): void
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Boolean types.');
    }

    public function visitDouble($data, array $type, Context $context): void
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Double types.');
    }

    public function visitInteger($data, array $type, Context $context): void
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Integer types.');
    }

    public function visitArray($data, array $type, Context $context): void
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit Array types.');
    }

    public function visitProperty(PropertyMetadata $metadata, $data, Context $context): void
    {
        throw new RuntimeException('PlainTextDeserializationVisitor cannot visit properties.');
    }

    public function getResult($data = null)
    {
        return $data;
    }
}
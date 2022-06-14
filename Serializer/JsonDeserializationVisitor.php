<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\Factory\DeserializationVisitorFactory;
use JMS\Serializer\JsonDeserializationVisitor as JsonDeserializationVisitorBase;

/**
 * Class JsonDeserializationVisitor.
 */
class JsonDeserializationVisitor implements DeserializationVisitorFactory
{
    /**
     * @var DeserializationTypesValidator
     */
    protected DeserializationTypesValidator $visitorValidator;

    private JsonDeserializationVisitorBase $jsonVisitor;

    /**
     * @param DeserializationTypesValidator $visitorValidator
     */
    public function __construct(DeserializationTypesValidator $visitorValidator) {
        $this->visitorValidator = $visitorValidator;
        $this->jsonVisitor = new JsonDeserializationVisitorBase();
    }

    public function visitString($data, array $type, Context $context): string
    {
        $this->visitorValidator->validateString($data, $context, $this->jsonVisitor->getCurrentObject());

        return $this->jsonVisitor->visitString($data, $type);
    }

    public function visitBoolean($data, array $type, Context $context): bool
    {
        $this->visitorValidator->validateBoolean($data, $context, $this->jsonVisitor->getCurrentObject());

        return $this->jsonVisitor->visitBoolean($data, $type);
    }

    public function visitDouble($data, array $type, Context $context): float
    {
        $this->visitorValidator->validateDouble($data, $context, $this->jsonVisitor->getCurrentObject());

        return $this->jsonVisitor->visitDouble($data, $type);
    }

    public function visitInteger($data, array $type, Context $context): int
    {
        $this->visitorValidator->validateInteger($data, $context, $this->jsonVisitor->getCurrentObject());

        return $this->jsonVisitor->visitInteger($data, $type);
    }

    public function getVisitor(): DeserializationVisitorInterface
    {
        return $this->jsonVisitor;
    }
}
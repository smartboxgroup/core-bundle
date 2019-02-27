<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Context;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\JsonDeserializationVisitor as JMSJsonDeserializationVisitor;

/**
 * Class JsonDeserializationVisitor.
 */
class JsonDeserializationVisitor
{
    /**
     * @var DeserializationTypesValidator
     */
    protected $visitorValidator;

    /**
     * @var JMSJsonDeserializationVisitor
     */
    protected $visitor;

    /**
     * @param PropertyNamingStrategyInterface $namingStrategy
     * @param ObjectConstructorInterface      $objectConstructor
     * @param DeserializationTypesValidator   $visitorValidator
     * @param JMSJsonDeserializationVisitor   $visitor
     */
    public function __construct(
        PropertyNamingStrategyInterface $namingStrategy,
        ObjectConstructorInterface $objectConstructor,
        DeserializationTypesValidator $visitorValidator,
        JMSJsonDeserializationVisitor $visitor
    ) {
        $this->visitor = $visitor;
//        parent::__construct($namingStrategy);
//        $this->visitor->setCurrentMetadata($namingStrategy);
        $visitorValidator->setNamingStrategy($namingStrategy);
        $this->visitorValidator = $visitorValidator;

    }

    public function visitString($data, array $type, Context $context)
    {
        $this->visitorValidator->validateString($data, $context, $this->getCurrentObject());

        return $this->visitor->visitString($data, $type, $context);
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        $this->visitorValidator->validateBoolean($data, $context, $this->getCurrentObject());

        return $this->visitor->visitBoolean($data, $type, $context);
    }

    public function visitDouble($data, array $type, Context $context)
    {
        $this->visitorValidator->validateDouble($data, $context, $this->getCurrentObject());

        return $this->visitor->visitDouble($data, $type, $context);
    }

    public function visitInteger($data, array $type, Context $context)
    {
        $this->visitorValidator->validateInteger($data, $context, $this->getCurrentObject());

        return $this->visitor->visitInteger($data, $type, $context);
    }

    /**
     * @return JMSJsonDeserializationVisitor
     */
    public function getVisitor(): JMSJsonDeserializationVisitor
    {
        return $this->visitor;
    }

    /**
     * @param JMSJsonDeserializationVisitor $visitor
     */
    public function setVisitor(JMSJsonDeserializationVisitor $visitor): void
    {
        $this->visitor = $visitor;
    }


}

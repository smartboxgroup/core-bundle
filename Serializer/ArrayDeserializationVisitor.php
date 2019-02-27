<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Context;
use JMS\Serializer\GenericDeserializationVisitor;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

/**
 * Class ArrayDeserializationVisitor.
 */
class ArrayDeserializationVisitor extends AbstractArrayDeserializationVisitor
{
    /**
     * @var DeserializationTypesValidator
     */
    protected $visitorValidator;

    /**
     * @param PropertyNamingStrategyInterface $namingStrategy
     * @param ObjectConstructorInterface      $objectConstructor
     * @param DeserializationTypesValidator   $visitorValidator
     */
    public function __construct(
        PropertyNamingStrategyInterface $namingStrategy,
        ObjectConstructorInterface $objectConstructor,
        DeserializationTypesValidator $visitorValidator
    ) {
//        parent::__construct($namingStrategy);

        $visitorValidator->setNamingStrategy($namingStrategy);
        $this->visitorValidator = $visitorValidator;
    }

    protected function decode($value)
    {
        return (array) $value;
    }

    public function visitString($data, array $type, Context $context)
    {
        $this->visitorValidator->validateString($data, $context, $this->getCurrentObject());

        return parent::visitString($data, $type, $context);
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        $this->visitorValidator->validateBoolean($data, $context, $this->getCurrentObject());

        return parent::visitBoolean($data, $type, $context);
    }

    public function visitDouble($data, array $type, Context $context)
    {
        $this->visitorValidator->validateDouble($data, $context, $this->getCurrentObject());

        return parent::visitDouble($data, $type, $context);
    }

    public function visitInteger($data, array $type, Context $context)
    {
        $this->visitorValidator->validateInteger($data, $context, $this->getCurrentObject());

        return parent::visitInteger($data, $type, $context);
    }
}

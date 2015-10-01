<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Context;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

/**
 * Class JsonDeserializationVisitor
 * @package Smartbox\CoreBundle\Serializer
 */
class JsonDeserializationVisitor extends \JMS\Serializer\JsonDeserializationVisitor
{
    /**
     * @var DeserializationTypesValidator
     */
    protected $visitorValidator;

    /**
     * @param PropertyNamingStrategyInterface $namingStrategy
     * @param ObjectConstructorInterface $objectConstructor
     * @param DeserializationTypesValidator $visitorValidator
     */
    public function __construct(PropertyNamingStrategyInterface $namingStrategy,
                                ObjectConstructorInterface $objectConstructor,
                                DeserializationTypesValidator $visitorValidator)
    {
        parent::__construct($namingStrategy, $objectConstructor);

        $visitorValidator->setNamingStrategy($this->namingStrategy);
        $this->visitorValidator = $visitorValidator;
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

        return parent::visitDouble($data, $type, $context);    }

    public function visitInteger($data, array $type, Context $context)
    {
        $this->visitorValidator->validateInteger($data, $context, $this->getCurrentObject());

        return parent::visitInteger($data, $type, $context);
    }
}

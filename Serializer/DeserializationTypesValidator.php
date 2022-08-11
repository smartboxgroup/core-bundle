<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException;

class DeserializationTypesValidator
{
    protected DeserializationCastingCheckerInterface $castingChecker;

    protected PropertyNamingStrategyInterface $namingStrategy;

    public function __construct(DeserializationCastingCheckerInterface $castingChecker)
    {
        $this->castingChecker = $castingChecker;
    }

    public function validateString($data, Context $context, $currentObject = null): void
    {
        if (
            null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)
        ) {
            if (!$this->castingChecker->canBeCastedToString($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'string', $currentObject
                );
            }
        }
    }

    public function validateBoolean($data, Context $context, $currentObject = null): void
    {
        if (
            null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)
        ) {
            if (!$this->castingChecker->canBeCastedToBoolean($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'boolean', $currentObject
                );
            }
        }
    }

    public function validateDouble($data, Context $context, $currentObject = null): void
    {
        if (
            null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)
        ) {
            if (!$this->castingChecker->canBeCastedToDouble($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'double', $currentObject
                );
            }
        }
    }

    public function validateInteger($data, Context $context, $currentObject = null): void
    {
        if (
            null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)
        ) {
            if (!$this->castingChecker->canBeCastedToInteger($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'integer', $currentObject
                );
            }
        }
    }

    private function getCurrentPropertyMetadata(Context $context): PropertyMetadata
    {
        return $context->getMetadataStack()->top();
    }

    private function getCurrentPropertyName(Context $context): ?string
    {
        $property = null;
        if (null !== ($metadata = $this->getCurrentPropertyMetadata($context))) {
            if ($metadata->name) {
                $property = $this->namingStrategy->translateName($metadata);
            }
        }

        return $property;
    }

    private function getCurrentClassName(Context $context): ?string
    {
        $class = null;
        /** @var PropertyMetadata $metadata */
        if (null !== ($metadata = $context->getMetadataStack()->top())) {
            if ($metadata->class) {
                $class = $metadata->class;
            }
        }

        return $class;
    }
}
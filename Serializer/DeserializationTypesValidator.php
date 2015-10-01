<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\AbstractVisitor;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\VisitorInterface;
use Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException;

class DeserializationTypesValidator
{
    /**
     * @var DeserializationCastingCheckerInterface
     */
    protected $castingChecker;

    /**
     * @var PropertyNamingStrategyInterface
     */
    protected $namingStrategy;

    /**
     * @param DeserializationCastingCheckerInterface $castingChecker
     */
    public function __construct(DeserializationCastingCheckerInterface $castingChecker)
    {
        $this->castingChecker = $castingChecker;
    }

    /**
     * @param PropertyNamingStrategyInterface $namingStrategy
     */
    public function setNamingStrategy(PropertyNamingStrategyInterface $namingStrategy)
    {
        $this->namingStrategy = $namingStrategy;
    }

    public function validateString($data, Context $context, $currentObject)
    {
        if (null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()
                ->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)) {

            if (!$this->castingChecker->canBeCastedToString($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'string', $currentObject
                );
            }
        }
    }

    public function validateBoolean($data, Context $context, $currentObject)
    {
        if (null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()
                ->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)) {

            if (!$this->castingChecker->canBeCastedToBoolean($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'boolean', $currentObject
                );
            }
        }
    }

    public function validateDouble($data, Context $context, $currentObject)
    {
        if (null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()
                ->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)) {

            if (!$this->castingChecker->canBeCastedToDouble($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'double', $currentObject
                );
            }
        }
    }

    public function validateInteger($data, Context $context, $currentObject)
    {
        if (null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()
                ->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)) {

            if (!$this->castingChecker->canBeCastedToInteger($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'integer', $currentObject
                );
            }
        }
    }

    /**
     * @param Context $context
     * @return \JMS\Serializer\Metadata\PropertyMetadata
     */
    private function getCurrentPropertyMetadata(Context $context)
    {
        return $context->getMetadataStack()->top();
    }

    /**
     * @param Context $context
     * @return string|null
     */
    private function getCurrentPropertyName(Context $context)
    {
        $property = null;
        /** @var PropertyMetadata $metadata */
        if (null !== ($metadata = $this->getCurrentPropertyMetadata($context))) {
            if ($metadata->name) {
                $property = $this->namingStrategy->translateName($metadata);
            }
        }

        return $property;
    }

    /**
     * @param Context $context
     * @return string|null
     */
    private function getCurrentClassName(Context $context)
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
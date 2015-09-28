<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;
use Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException;

/**
 * Trait CastingCheckerAwareVisitor
 * @package Smartbox\CoreBundle\Serializer
 */
trait CastingCheckerVisitor
{
    /**
     * @var DeserializationCastingChecker
     */
    private $castingChecker;

    /**
     * {@inheritDoc}
     */
    public function visitString($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        if (null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()
                ->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)) {

            if (!$this->castingChecker->canBeCastedToString($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'string', $this->getCurrentObject()
                );
            }
        }
        return parent::visitString($data, $type, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function visitBoolean($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        if (null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()
                ->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)) {

            if (!$this->castingChecker->canBeCastedToBoolean($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'boolean', $this->getCurrentObject()
                );
            }
        }
        return parent::visitBoolean($data, $type, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function visitInteger($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        if (null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()
                ->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)) {

            if (!$this->castingChecker->canBeCastedToInteger($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'integer', $this->getCurrentObject()
                );
            }
        }

        return parent::visitInteger($data, $type, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function visitDouble($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        if (null === $context->getExclusionStrategy() ||
            !$context->getExclusionStrategy()
                ->shouldSkipProperty($this->getCurrentPropertyMetadata($context), $context)) {

            if (!$this->castingChecker->canBeCastedToDouble($data)) {
                throw new DeserializationTypeMismatchException(
                    $this->getCurrentPropertyName($context),
                    $this->getCurrentClassName($context),
                    $data, 'double', $this->getCurrentObject()
                );
            }
        }

        return parent::visitDouble($data, $type, $context);
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

    /**
     * @param $data
     * @return string
     */
    private function fixXmlNode($data) {
        if ($data instanceof \SimpleXMLElement) {

            if (in_array(dom_import_simplexml($data)->firstChild->nodeType, [XML_CDATA_SECTION_NODE, XML_TEXT_NODE])) {
                $data = $data->__toString();
            }
        }

        return $data;
    }
}

<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Context;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

/**
 * Class XmlDeserializationVisitor
 * @package Smartbox\CoreBundle\Serializer
 */
class XmlDeserializationVisitor extends \JMS\Serializer\XmlDeserializationVisitor
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
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateString($data, $context, $this->getCurrentObject());

        return parent::visitString($data, $type, $context);
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateBoolean($data, $context, $this->getCurrentObject());

        return parent::visitBoolean($data, $type, $context);
    }

    public function visitDouble($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateDouble($data, $context, $this->getCurrentObject());

        return parent::visitDouble($data, $type, $context);
    }

    public function visitInteger($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateInteger($data, $context, $this->getCurrentObject());

        return parent::visitInteger($data, $type, $context);
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

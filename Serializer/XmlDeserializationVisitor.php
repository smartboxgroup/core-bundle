<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Context;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\XmlDeserializationVisitor as JMSXmlDeserializationVisitor;

/**
 * Class XmlDeserializationVisitor.
 */
class XmlDeserializationVisitor
{
    /**
     * @var DeserializationTypesValidator
     */
    protected $visitorValidator;

    /**
     * @var JMSXmlDeserializationVisitor
     */
    protected $visitor;

    /**
     * @param PropertyNamingStrategyInterface $namingStrategy
     * @param ObjectConstructorInterface      $objectConstructor
     * @param DeserializationTypesValidator   $visitorValidator
     * @param JMSXmlDeserializationVisitor    $visitor
     */
    public function __construct(
        PropertyNamingStrategyInterface $namingStrategy,
        ObjectConstructorInterface $objectConstructor,
        DeserializationTypesValidator $visitorValidator,
        JMSXmlDeserializationVisitor $visitor
    ) {
//        parent::__construct($namingStrategy);
        $this->visitor = $visitor;
//        $this->visitor->setCurrentMetadata($namingStrategy);

        $visitorValidator->setNamingStrategy($namingStrategy);
        $this->visitorValidator = $visitorValidator;

    }

    public function visitString($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateString($data, $context, $this->getCurrentObject());

        return $this->visitor->visitString($data, $type, $context);
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateBoolean($data, $context, $this->getCurrentObject());

        return $this->visitor->visitBoolean($data, $type, $context);
    }

    public function visitDouble($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateDouble($data, $context, $this->getCurrentObject());

        return $this->visitor->visitDouble($data, $type, $context);
    }

    public function visitInteger($data, array $type, Context $context)
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateInteger($data, $context, $this->getCurrentObject());

        return $this->visitor->visitInteger($data, $type, $context);
    }

    /**
     * @param $data
     *
     * @return string
     */
    private function fixXmlNode($data)
    {
        if ($data instanceof \SimpleXMLElement) {
            if (\in_array(\dom_import_simplexml($data)->firstChild->nodeType, [XML_CDATA_SECTION_NODE, XML_TEXT_NODE])) {
                $data = $data->__toString();
            }
        }

        return $data;
    }

    /**
     * @return JMSXmlDeserializationVisitor
     */
    public function getVisitor(): JMSXmlDeserializationVisitor
    {
        return $this->visitor;
    }

    /**
     * @param JMSXmlDeserializationVisitor $visitor
     */
    public function setVisitor(JMSXmlDeserializationVisitor $visitor): void
    {
        $this->visitor = $visitor;
    }


}

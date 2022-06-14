<?php
declare(strict_types=1);

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\Factory\DeserializationVisitorFactory;
use JMS\Serializer\XmlDeserializationVisitor as xmlDeserializationVisitorBase;

/**
 * Class XmlDeserializationVisitor.
 */
class XmlDeserializationVisitor implements DeserializationVisitorFactory
{
    /**
     * @var DeserializationTypesValidator
     */
    protected DeserializationTypesValidator $visitorValidator;
    private xmlDeserializationVisitorBase $xmlVisitor;

    /**
     * @param DeserializationTypesValidator $visitorValidator
     */
    public function __construct(
        DeserializationTypesValidator $visitorValidator
    ) {
        $this->visitorValidator = $visitorValidator;
        $this->xmlVisitor = new xmlDeserializationVisitorBase();
    }

    public function visitString($data, array $type, Context $context): string
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateString($data, $context, $this->xmlVisitor->getCurrentObject());

        return $this->xmlVisitor->visitString($data, $type);
    }

    public function visitBoolean($data, array $type, Context $context): bool
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateBoolean($data, $context, $this->xmlVisitor->getCurrentObject());

        return $this->xmlVisitor->visitBoolean($data, $type);
    }

    public function visitDouble($data, array $type, Context $context): float
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateDouble($data, $context, $this->xmlVisitor->getCurrentObject());

        return $this->xmlVisitor->visitDouble($data, $type);
    }

    public function visitInteger($data, array $type, Context $context): int
    {
        $data = $this->fixXmlNode($data);

        $this->visitorValidator->validateInteger($data, $context, $this->xmlVisitor->getCurrentObject());

        return $this->xmlVisitor->visitInteger($data, $type);
    }

    /**
     * @param $data
     *
     * @return string
     */
    private function fixXmlNode($data)
    {
        if ($data instanceof \SimpleXMLElement) {
            if (
                \in_array(
                    \dom_import_simplexml($data)->firstChild->nodeType,
                    [XML_CDATA_SECTION_NODE, XML_TEXT_NODE],
                    true
                )
            ) {
                $data = $data->__toString();
            }
        }

        return $data;
    }

    public function getVisitor(): DeserializationVisitorInterface
    {
        return $this->xmlVisitor;
    }
}
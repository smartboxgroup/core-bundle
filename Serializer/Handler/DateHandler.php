<?php

namespace Smartbox\CoreBundle\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GenericDeserializationVisitor;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\GraphNavigator;

/**
 * Based on JMS\Serializer\Handler\DateHandler
 * It was not possible to extend the original class due to private fields
 * Class DateHandler.
 */
class DateHandler implements SubscribingHandlerInterface
{
    /** @var  \JMS\Serializer\Handler\DateHandler */
    protected $decoratedDateHandler;

    private $defaultFormat;
    private $defaultTimezone;
    private $xmlCData;

    public static function getSubscribingMethods()
    {
        $methods = \JMS\Serializer\Handler\DateHandler::getSubscribingMethods();

        $types = ['DateTime', 'DateInterval'];

        $newFormats = ['array'];   // Add here any new format you wish to support

        foreach ($newFormats as $format) {
            $methods[] = [
                'type' => 'DateTime',
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => $format,
            ];

            foreach ($types as $type) {
                $methods[] = [
                    'type' => $type,
                    'format' => $format,
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'method' => 'serialize'.$type,
                ];
            }
        }

        return $methods;
    }

    public function __construct($defaultFormat = 'Y-m-d\TH:i:s.uP', $defaultTimezone = 'UTC', $xmlCData = true)
    {
        $this->defaultFormat = $defaultFormat;
        $this->defaultTimezone = new \DateTimeZone($defaultTimezone);
        $this->xmlCData = $xmlCData;
        $this->decoratedDateHandler = new \JMS\Serializer\Handler\DateHandler(
            $defaultFormat,
            $defaultTimezone,
            $xmlCData
        );
    }

    public function serializeDateTime(VisitorInterface $visitor, \DateTime $date, array $type, Context $context)
    {
        return $this->decoratedDateHandler->serializeDateTime($visitor, $date, $type, $context);
    }

    public function serializeDateInterval(VisitorInterface $visitor, \DateInterval $date, array $type, Context $context)
    {
        return $this->decoratedDateHandler->serializeDateInterval($visitor, $date, $type, $context);
    }

    public function deserializeDateTimeFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->decoratedDateHandler->deserializeDateTimeFromXml($visitor, $data, $type);
    }

    public function deserializeDateTimeFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->decoratedDateHandler->deserializeDateTimeFromJson($visitor, $data, $type);
    }

    private function parseDateTime($data, array $type)
    {
        $timezone = isset($type['params'][1]) ? new \DateTimeZone($type['params'][1]) : $this->defaultTimezone;
        $format = $this->getFormat($type);
        $datetime = \DateTime::createFromFormat($format, (string) $data, $timezone);
        if (false === $datetime) {
            throw new RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $format));
        }

        return $datetime;
    }

    public function deserializeDateTimeFromArray(GenericDeserializationVisitor $visitor, $data, array $type)
    {
        if (null === $data) {
            return;
        }

        return $this->parseDateTime($data, $type);
    }

    /**
     * @return string
     *
     * @param array $type
     */
    private function getFormat(array $type)
    {
        return isset($type['params'][0]) ? $type['params'][0] : $this->defaultFormat;
    }
}

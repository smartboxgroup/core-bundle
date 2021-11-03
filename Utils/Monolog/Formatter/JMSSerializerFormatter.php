<?php

namespace Smartbox\CoreBundle\Utils\Monolog\Formatter;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Monolog\Formatter\JsonFormatter;

/**
 * Class JMSSerializerFormatter.
 */
class JMSSerializerFormatter extends JsonFormatter
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        $context = new SerializationContext();
        $context->setGroups(['logs']);
        $context->setSerializeNull(true);

        return $this->serializer->serialize($record, 'json', $context);
    }
}

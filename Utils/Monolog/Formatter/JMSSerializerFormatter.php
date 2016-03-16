<?php

namespace Smartbox\CoreBundle\Utils\Monolog\Formatter;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Monolog\Formatter\JsonFormatter;

/**
 * Class JMSSerializerFormatter
 * @package Smartbox\CoreBundle\Utils\Monolog\Formatter
 */
class JMSSerializerFormatter extends JsonFormatter
{
    const _USE_JSON_ENCODE = '_use_json_encode';

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
    public function format(array $record)
    {
        if (isset($record['context'][self::_USE_JSON_ENCODE])) {
            $useJsonEncode = $record['context'][self::_USE_JSON_ENCODE];
            unset($record['context'][self::_USE_JSON_ENCODE]);

            if (true === $useJsonEncode) {
                // this hack is related with a 'smartcore.cache_service' which uses internally monolog (and this formatter) in case of Redis is down
                // serializer doesn't work with recursive calls
                // so when we try to serialize something (while logging message by monolog) in the middle of another serialization it fails
                // pull request to fix this is still opened
                // https://github.com/schmittjoh/serializer/pull/341
                return json_encode($record);
            }
        }

        $context = new SerializationContext();
        $context->setGroups(['logs']);
        $context->setSerializeNull(true);

        return $this->serializer->serialize($record, 'json', $context);
    }
}
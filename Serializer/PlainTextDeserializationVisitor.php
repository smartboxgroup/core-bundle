<?php

namespace Smartbox\CoreBundle\Serializer;

/**
 * This is a really simple visitor that will effectively bypass JMS. Useful for REST APIs that are not truly REST APIs
 * and return plain text responses, which JMS by default refuses to process because they are not real JSON.
 */
class PlainTextDeserializationVisitor extends \JMS\Serializer\JsonDeserializationVisitor
{
    protected function decode($str)
    {
        return $str;
    }
}

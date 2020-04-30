<?php

namespace Smartbox\CoreBundle\Serializer;

/**
 * Class JsonDeserializationVisitor.
 */
class PlainTextDeserializationVisitor extends \JMS\Serializer\JsonDeserializationVisitor
{
    protected function decode($str)
    {
        return $str;
    }
}

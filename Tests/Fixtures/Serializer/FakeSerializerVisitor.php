<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Serializer;

use JMS\Serializer\Context;

class FakeSerializerVisitor
{
    public function visitString($data, array $type, Context $context) {}
    public function visitBoolean($data, array $type, Context $context) {}
    public function visitInteger($data, array $type, Context $context) {}
    public function visitDouble($data, array $type, Context $context) {}
    public function visitArray($data, array $type, Context $context) {}
}

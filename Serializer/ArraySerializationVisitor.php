<?php

namespace Smartbox\CoreBundle\Serializer;

use JMS\Serializer\GenericSerializationVisitor;

class ArraySerializationVisitor extends GenericSerializationVisitor
{
    public function getResult()
    {
        return $this->getRoot();
    }
}
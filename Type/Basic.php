<?php

namespace Smartbox\CoreBundle\Type;


use Smartbox\CoreBundle\Type\SerializableInterface;
use Smartbox\CoreBundle\Type\Traits\HasInternalType;

abstract class Basic implements SerializableInterface
{
    use HasInternalType;

    abstract public function setValue($value);

    public function __toString()
    {
        return (string)$this->getValue();
    }

    abstract public function getValue();

}
<?php

namespace Smartbox\CoreBundle\Type;


use Smartbox\CoreBundle\Type\SerializableInterface;
use Smartbox\CoreBundle\Type\Traits\HasType;

abstract class Basic implements SerializableInterface
{
    use HasType;

    abstract public function setValue($value);

    public function __toString()
    {
        return (string)$this->getValue();
    }

    abstract public function getValue();

}
<?php

namespace Smartbox\CoreBundle\Entity\BasicTypes;


use Smartbox\CoreBundle\Entity\Entity;

abstract class Basic extends Entity
{

    abstract public function setValue($value);

    public function __toString()
    {
        return (string)$this->getValue();
    }

    abstract public function getValue();

}
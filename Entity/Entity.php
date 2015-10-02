<?php

namespace Smartbox\CoreBundle\Entity;


use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Entity\Traits\HasGroup;
use Smartbox\CoreBundle\Entity\Traits\HasType;
use Smartbox\CoreBundle\Entity\Traits\HasVersion;

class Entity implements EntityInterface
{
    use HasGroup;
    use HasVersion;
    use HasType;

    public function __construct()
    {
    }
}
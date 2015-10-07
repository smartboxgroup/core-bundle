<?php
namespace Smartbox\CoreBundle\Type\Traits;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

trait HasVersion
{

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"metadata"})
     * @var  string
     */
    protected $version;

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        if (!empty($version) && !is_string($version)) {
            throw new \InvalidArgumentException("Expected null or string in method setVersion");
        }

        $this->version = $version;
    }
}
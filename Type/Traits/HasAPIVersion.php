<?php

namespace Smartbox\CoreBundle\Type\Traits;

use JMS\Serializer\Annotation as JMS;

trait HasAPIVersion
{
    /**
     * @JMS\Type("string")
     * @JMS\Expose
     * @JMS\SerializedName("_apiVersion")
     * @JMS\Groups({"metadata"})
     *
     * @var string
     */
    protected string $version;

    /**
     * @return string
     */
    public function getAPIVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setAPIVersion(string $version): void
    {
        if (!empty($version) && !\is_string($version)) {
            throw new \InvalidArgumentException('Expected null or string in method setVersion');
        }

        $this->version = $version;
    }
}
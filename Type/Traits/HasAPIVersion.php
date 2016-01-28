<?php
namespace Smartbox\CoreBundle\Type\Traits;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

trait HasAPIVersion
{

    /**
     * @JMS\Type("string")
     * @JMS\Expose
     * @JMS\SerializedName("_apiVersion")
     * @JMS\Groups({"metadata"})
     * @var  string
     */
    protected $version;

    /**
     * @return string
     */
    public function getAPIVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setAPIVersion($version)
    {
        if (!empty($version) && !is_string($version)) {
            throw new \InvalidArgumentException("Expected null or string in method setVersion");
        }

        $this->version = $version;
    }
}
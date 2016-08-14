<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Smartbox\CoreBundle\Type\Entity;

class TestEntity extends Entity
{
    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_LOGS, EntityConstants::GROUP_A})
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $title;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_LOGS, EntityConstants::GROUP_A, EntityConstants::GROUP_B, EntityConstants::GROUP_C})
     * @JMS\Since(EntityConstants::VERSION_2)
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $description;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @JMS\Groups({EntityConstants::GROUP_LOGS, EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $note;

    /**
     * @Assert\Type(type="boolean")
     * @Assert\NotBlank
     * @JMS\Groups({EntityConstants::GROUP_LOGS, EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @JMS\Type("boolean")
     * @JMS\Expose
     */
    protected $enabled;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param $note
     *
     * @return $this
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)$this->enabled;
    }

    /**
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}

<?php


namespace Smartbox\CoreBundle\Entity;


interface EntityInterface
{

    public function __construct();

    /**
     * @return string
     */
    public function getVersion();

    /**
     * @param string $version
     */
    public function setVersion($version);

    /**
     * @return string
     */
    public function getGroup();

    /**
     * @param string $group
     */
    public function setGroup($group);

    /**
     * @return string
     */
    public function getType();
}
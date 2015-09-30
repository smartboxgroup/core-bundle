<?php

namespace Smartbox\CoreBundle\Entity;

interface EntityInterface
{
    const GROUP_PUBLIC = 'public';
    const GROUP_METADATA = 'metadata';
    const GROUP_DEFAULT = 'Default';

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

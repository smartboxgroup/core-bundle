<?php

namespace Smartbox\CoreBundle\Type;

interface EntityInterface extends SerializableInterface
{
    const GROUP_PUBLIC = 'public';
    const GROUP_METADATA = 'metadata';
    const GROUP_DEFAULT = 'Default';

    public function __construct();

    /**
     * @return string
     */
    public function getAPIVersion();

    /**
     * @param string $version
     */
    public function setAPIVersion($version);

    /**
     * @return string
     */
    public function getEntityGroup();

    /**
     * @param string $group
     */
    public function setEntityGroup($group);
}

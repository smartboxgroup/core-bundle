<?php

namespace Smartbox\CoreBundle\Type;

interface EntityInterface extends SerializableInterface
{
    public const GROUP_PUBLIC = 'public';
    public const GROUP_METADATA = 'metadata';
    public const GROUP_DEFAULT = 'Default';

    public function __construct();

    /**
     * @return string
     */
    public function getAPIVersion(): string;

    /**
     * @param string $version
     */
    public function setAPIVersion(string $version): void;

    /**
     * @return string
     */
    public function getEntityGroup(): string;

    /**
     * @param string $group
     */
    public function setEntityGroup(string $group): void;
}
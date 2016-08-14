<?php

namespace Smartbox\CoreBundle\Utils\Helper;

class NamespaceResolver
{
    /**
     * @var array
     */
    protected $namespaces = [];

    public function __construct(array $namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * @param $class
     *
     * @return string
     */
    public function resolveNamespaceForClass($class)
    {
        if (class_exists($class)) {
            return $class;
        }
        
        foreach ($this->namespaces as $namespace) {
            if (class_exists($namespace.'\\'.$class)) {
                return $namespace.'\\'.$class;
            }
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Class %s doesn\'t exist in configured namespaces',
                $class
            )
        );
    }
}

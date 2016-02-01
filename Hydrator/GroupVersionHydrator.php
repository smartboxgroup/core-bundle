<?php

namespace Smartbox\CoreBundle\Hydrator;

use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\MetadataFactoryInterface;
use Smartbox\CoreBundle\Type\EntityInterface;

/**
 * Class GroupVersionHydrator
 * @package Smartbox\CoreBundle\Hydrator
 *
 * Hydrate an entity (or an array of entities) by propagating it's current version and group to any eventual sub-entity
 */
class GroupVersionHydrator
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * Constructor
     *
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Hydrates an object (it expected to be an entity or an array of entities) by setting a given group and a given
     * version to it and to all its sub-fields/sub-entities recursively
     *
     * @param array|EntityInterface $object
     * @param string $group
     * @param string $version
     * @return array|EntityInterface
     */
    public function hydrate($object, $group, $version)
    {
        if (is_array($object)) {
            return $this->hydrateArray($object, $group, $version);
        }

        if (!$object instanceof EntityInterface) {
            throw new \InvalidArgumentException('The given object is not an array and neither an entity');
        }

        return $this->hydrateEntity($object, $group, $version);
    }

    /**
     * Hydrate an array
     *
     * @param array $array
     * @param string $group
     * @param string $version
     * @return array
     */
    private function hydrateArray($array, $group, $version)
    {
        foreach ($array as $key => $value) {
            $this->hydrate($value, $group, $version);
        }

        return $array;
    }

    /**
     * Hydrate an entity
     *
     * @param EntityInterface $entity
     * @param string $group
     * @param string $version
     * @return EntityInterface
     */
    private function hydrateEntity(EntityInterface $entity, $group, $version)
    {
        $entity->setEntityGroup($group);
        $entity->setAPIVersion($version);

        // hydrate all the sub-fields recursively using JMS to extract them and identify their type
        $metadata = $this->metadataFactory->getMetadataForClass(get_class($entity));
        /** @var PropertyMetadata $property */
        foreach ($metadata->propertyMetadata as $property) {
            if ($property->type['name'] === 'array') {
                $array = $property->getValue($entity);
                if (is_array($array)) {
                    $this->hydrateArray($array, $group, $version);
                }
            } elseif (($object = $property->getValue($entity)) instanceof EntityInterface) {
                $this->hydrateEntity($object, $group, $version);
            }
        }

        return $entity;
    }
}

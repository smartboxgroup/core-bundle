<?php

namespace Smartbox\CoreBundle\Utils\Generator;

use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Exclusion\VersionExclusionStrategy;
use Metadata\MetadataFactoryInterface;
use Smartbox\CoreBundle\Type\Context\ContextFactory;
use Smartbox\CoreBundle\Type\Entity;
use Smartbox\CoreBundle\Type\EntityInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class RandomFixtureGenerator
{
    /**
     * @var MetadataFactoryInterface
     */
    protected $metadataFactory;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param $entityNamespace
     * @param null $group
     * @param null $version
     * @return EntityInterface
     */
    public function generate($entityNamespace, $group = null, $version = null)
    {
        if (!is_subclass_of($entityNamespace, EntityInterface::class)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Entity class: %s should implement an %s',
                    $entityNamespace,
                    EntityInterface::class
                )
            );
        }

        /** @var Entity $entity */
        $entity = new $entityNamespace();
        $entity->setEntityGroup($group);
        $entity->setAPIVersion($version);

        $this->fillEntityWithRandomData($entity);

        return $entity;
    }

    protected function fillEntityWithRandomData(EntityInterface $entity)
    {
        $version = $entity->getAPIVersion();
        $group = $entity->getEntityGroup();
        $versionExclusion = new VersionExclusionStrategy($version);
        $groupExclusion = new GroupsExclusionStrategy(array($group, Entity::GROUP_METADATA, Entity::GROUP_PUBLIC));
        $propertyAccessor = new PropertyAccessor();
        $context = ContextFactory::createSerializationContextForFixtures($group, $version);

        $metadata = $this->metadataFactory->getMetadataForClass(get_class($entity));

        foreach ($metadata->propertyMetadata as $property => $propertyMetadata) {
            if (!in_array($property, array('entityGroup', 'version', '_type'))
                && $propertyAccessor->isWritable($entity, $property)
                && (!$group || !$groupExclusion->shouldSkipProperty($propertyMetadata, $context))
                && (!$version || !$versionExclusion->shouldSkipProperty($propertyMetadata, $context))
            ) {
                $typeName = @$propertyMetadata->type['name'];
                $typeParams = @$propertyMetadata->type['params'];

                if (empty($typeName)) {
                    continue;
                }

                try {
                    $data = $this->generateRandomData($typeName, $typeParams, $group, $version);
                } catch (\Exception $e) {
                    throw new \Exception('Property: ' . $property . 'Error: ' . $e->getMessage());
                }

                $propertyAccessor->setValue($entity, $property, $data);
            }
        }
    }

    /**
     * @param $typeName
     * @param array|null $typeParams
     * @param null $group
     * @param null $version
     * @return float|int|string|array|\DateTime|EntityInterface
     */
    protected function generateRandomData($typeName, array $typeParams = null, $group = null, $version = null)
    {
        $result = null;

        switch ($typeName) {
            case 'DateTime':
                $result = new \DateTime();
                break;

            case 'integer':
                $result = rand(0, 100);
                break;

            case 'double':
                $result = doubleval(rand(0, 1000)/1000);
                break;

            case 'string':
                $result = substr(md5(rand()), 0, rand(2, 10));
                break;

            case 'array':
                $numberOfTypeParams = count($typeParams);

                if ($numberOfTypeParams == 2) {
                    $subtypeName = @$typeParams[0]['name'];
                    $subtypeParams = $typeParams[1]['name'];
                } elseif ($numberOfTypeParams == 1) {
                    $subtypeName = $typeParams[0]['name'];
                    $subtypeParams = null;
                } else {
                    throw new \RuntimeException('Missing JMS type params.');
                }

                $amountOfArrayIndexes = rand(0, 5);

                $result = [];
                while ($amountOfArrayIndexes-- > 0) {
                    $result[] = $this->generateRandomData($subtypeName, (array) $subtypeParams, $group, $version);
                }

                break;

            default:
                if (is_string($typeName) && class_exists($typeName)) {
                    $result = $this->generate($typeName, $group, $version);
                } else {
                    throw new \RuntimeException("Unrecognized type: $typeName");
                }
        }

        return $result;
    }
}
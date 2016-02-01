<?php

namespace Smartbox\CoreBundle\Validation;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;
use Smartbox\CoreBundle\Type\Entity;
use Smartbox\CoreBundle\Type\EntityInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidatorWithExclusion extends ValidatorDecorator
{

    /**
     * @var MetadataFactoryInterface  JMS Metadata factory
     */
    protected $metadataFactory;

    /**
     * @return MetadataFactoryInterface
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function setMetadataFactory($metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Returns true if a property of a given object should be ignored in the validation
     *
     * @param $object
     * @param $propertyPath
     * @return bool
     */
    protected function shouldSkip($object, $propertyPath)
    {
        if (!is_object($object) && !is_array($object)) {
            return false;
        }

        if (!empty($propertyPath)) {
            $parts = explode('.', $propertyPath);
            $accessor = new PropertyAccessor();
            $parent = $object;
            foreach ($parts as $childSubPath) {
                if ($parent instanceof EntityInterface && $this->shouldSkipObjectProperty($parent, $childSubPath)) {
                    return true;
                }

                if ($accessor->isReadable($parent, $childSubPath)) {
                    $parent = $accessor->getValue($parent, $childSubPath);
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    public function shouldSkipObjectProperty($object, $propertySubPath)
    {
        if ($object instanceof EntityInterface) {
            $version = $object->getAPIVersion();
            $groups = null;
            if ($object->getEntityGroup()) {
                $groups = array($object->getEntityGroup());
            }

            $className = get_class($object);
            $meta = $this->metadataFactory->getMetadataForClass($className);

            $fieldName = preg_replace('/\[[0-9]+\]/','',$propertySubPath);

            if (array_key_exists($fieldName, $meta->propertyMetadata)) {
                $propertyMeta = $meta->propertyMetadata[$fieldName];
                $context = SerializationContext::create();

                $exclusionStrategies = array();
                if ($groups) {
                    $exclusionStrategies[] = new GroupsExclusionStrategy($groups);
                }

                if ($version) {
                    $exclusionStrategies[] = new VersionExclusionStrategy($version);
                }

                // Apply exclusion strategies
                /** @var ExclusionStrategyInterface $strategy */
                foreach ($exclusionStrategies as $strategy) {
                    if (true === $strategy->shouldSkipProperty($propertyMeta, $context)) {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }

        return false;
    }

    public function validate($value, $constraints = null, $groups = null)
    {
        if (is_object($value) && $value instanceof EntityInterface) {
            if (!$groups && $value->getEntityGroup()) {
                $groups = array($value->getEntityGroup(), EntityInterface::GROUP_DEFAULT);
            }
        }

        $validation = parent::validate($value, $constraints, $groups);

        /** @var ConstraintViolation $validationError */
        foreach ($validation as $index => $validationError) {
            if ($this->shouldSkip($value, $validationError->getPropertyPath())) {
                $validation->remove($index);
            }
        }

        return $validation;
    }

    public function getObjectPropertiesPathsForPath($propertyPath)
    {
        $result = array();
        $parts = explode('.', $propertyPath);
        $parentPath = "";
        foreach ($parts as $child) {
            if (!empty($parentPath)) {
                $childPath = $parentPath.".".$child;
                $result[] = array($parentPath, $childPath);
                $parentPath = $childPath;
            } else {
                $parentPath = $child;
            }
        }

        return $result;
    }

    public function validateProperty($object, $propertyName, $groups = null)
    {
        if ($this->shouldSkip($object, $propertyName)) {
            return new ConstraintViolationList();
        }

        return parent::validateProperty($object, $propertyName, $groups);
    }


}
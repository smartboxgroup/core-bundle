<?php

namespace Smartbox\CoreBundle\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception;
use Symfony\Component\Validator\MetadataInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ValidatorDecorator implements ValidatorInterface
{
    /** @var ValidatorInterface */
    protected $decoratedValidator;

    /**
     * @return ValidatorInterface
     */
    public function getDecoratedValidator()
    {
        return $this->decoratedValidator;
    }

    /**
     * @param ValidatorInterface $decoratedValidator
     */
    public function setDecoratedValidator($decoratedValidator)
    {
        $this->decoratedValidator = $decoratedValidator;
    }

    /**
     * Returns the metadata for the given value.
     *
     * @param mixed $value Some value
     *
     * @return MetadataInterface The metadata for the value
     *
     * @throws Exception\NoSuchMetadataException If no metadata exists for the given value
     */
    public function getMetadataFor($value)
    {
        return $this->decoratedValidator->getMetadataFor($value);
    }

    /**
     * Returns whether the class is able to return metadata for the given value.
     *
     * @param mixed $value Some value
     *
     * @return bool Whether metadata can be returned for that value
     */
    public function hasMetadataFor($value)
    {
        return $this->decoratedValidator->hasMetadataFor($value);
    }

    /**
     * Validates a value against a constraint or a list of constraints.
     *
     * If no constraint is passed, the constraint {@link \Symfony\Component\Validator\Constraints\Valid} is assumed.
     *
     * @param mixed                   $value       The value to validate
     * @param Constraint|Constraint[] $constraints The constraint(s) to validate against
     * @param array|null              $groups      The validation groups to validate. If none is given, "Default" is assumed
     *
     * @return ConstraintViolationListInterface A list of constraint violations. If the list is empty, validation
     *                                          succeeded
     */
    public function validate($value, $constraints = null, $groups = null)
    {
        return $this->decoratedValidator->validate($value, $constraints, $groups);
    }

    /**
     * Validates a property of an object against the constraints specified for this property.
     *
     * @param object     $object       The object
     * @param string     $propertyName The name of the validated property
     * @param array|null $groups       The validation groups to validate. If none is given, "Default" is assumed
     *
     * @return ConstraintViolationListInterface A list of constraint violations. If the list is empty, validation
     *                                          succeeded
     */
    public function validateProperty($object, $propertyName, $groups = null)
    {
        return $this->decoratedValidator->validateProperty($object, $propertyName, $groups);
    }

    /**
     * Validates a value against the constraints specified for an object's property.
     *
     * @param object|string $objectOrClass The object or its class name
     * @param string        $propertyName  The name of the property
     * @param mixed         $value         The value to validate against the property's constraints
     * @param array|null    $groups        The validation groups to validate. If none is given, "Default" is assumed
     *
     * @return ConstraintViolationListInterface A list of constraint violations. If the list is empty, validation
     *                                          succeeded
     */
    public function validatePropertyValue($objectOrClass, $propertyName, $value, $groups = null)
    {
        return $this->decoratedValidator->validatePropertyValue($objectOrClass, $propertyName, $value, $groups);
    }

    /**
     * Starts a new validation context and returns a validator for that context.
     *
     * The returned validator collects all violations generated within its context. You can access these violations with
     * the {@link ContextualValidatorInterface::getViolations()} method.
     *
     * @return ContextualValidatorInterface The validator for the new context
     */
    public function startContext()
    {
        return $this->decoratedValidator->startContext();
    }

    /**
     * Returns a validator in the given execution context.
     *
     * The returned validator adds all generated violations to the given context.
     *
     * @param ExecutionContextInterface $context The execution context
     *
     * @return ContextualValidatorInterface The validator for that context
     */
    public function inContext(ExecutionContextInterface $context)
    {
        return $this->inContext($context);
    }
}

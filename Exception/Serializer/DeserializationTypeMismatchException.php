<?php

namespace Smartbox\CoreBundle\Exception\Serializer;

use JMS\Serializer\Annotation as JMS;

/**
 * Class DeserializationTypeMismatchException
 * @package Smartbox\CoreBundle\Exception\Serializer
 *
 * @JMS\ExclusionPolicy("all")
 */
class DeserializationTypeMismatchException
    extends \Exception
    implements \JMS\Serializer\Exception\Exception
{
    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $className;

    /**
     * @var mixed
     */
    private $propertyValue;

    /**
     * @var string
     */
    private $expectedType;

    /**
     * @var mixed
     *
     * @JMS\Expose
     * @JMS\Groups({"logs"})
     */
    private $originalData;

    /**
     * Constructor
     *
     * @param string $propertyName
     * @param string $className
     * @param mixed $propertyValue
     * @param string $expectedType
     * @param mixed $originalData
     */
    public function __construct($propertyName, $className, $propertyValue, $expectedType, $originalData)
    {
        $this->propertyName = $propertyName;
        $this->className = $className;
        $this->propertyValue = $propertyValue;
        $this->expectedType = $expectedType;
        $this->originalData = $originalData;

        $message = 'Type mismatch';
        if ($this->propertyName && $this->className) {
            $message .= sprintf(' in property "%s" while deserializing for "%s', $this->propertyName, $this->className);
        }
        $message .= sprintf(': found "%s", hence "%s" was expected',
            $this->getTypeOrClass($this->propertyValue),
            $this->expectedType
        );

        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return mixed
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * @return string
     */
    public function getExpectedType()
    {
        return $this->expectedType;
    }

    /**
     * @return mixed
     */
    public function getOriginalData()
    {
        return $this->originalData;
    }

    /**
     * @param mixed $data
     * @return string
     */
    private function getTypeOrClass($data)
    {
        if (is_object($data)) {
            return get_class($data);
        }

        return gettype($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return get_class($this);
    }
}

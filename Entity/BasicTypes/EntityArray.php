<?php

namespace Smartbox\CoreBundle\Entity\BasicTypes;


use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class EntityArray extends Entity
{

    /**
     * @var array
     * @JMS\Type("array<string,Smartbox\CoreBundle\Entity\Entity>")
     * @JMS\XmlMap(inline = true)
     */
    protected $array;

    public function __construct($input = array())
    {
        $this->array = array();

        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            $this->set(0, $input);
        }
    }

    /**
     * @param string $key
     * @param Entity $value
     */
    public function set($key, $value)
    {
        if (!is_string($key) && !is_numeric($key)) {
            throw new \InvalidArgumentException("Invalid key");
        }

        $cleanValue = false;

        if (is_scalar($value)) {
            if (is_string($value)) {
                $cleanValue = new String($value);
            } elseif (is_int($value)) {
                $cleanValue = new Integer($value);
            } elseif (is_double($value)) {
                $cleanValue = new Double($value);
            }
        } elseif (is_array($value) && count($value) > 0) {
            $cleanValue = new EntityArray($value);
        } elseif (is_object($value) && $value instanceof Entity) {
            $cleanValue = $value;
        }

        if ($cleanValue === false) {
            throw new \InvalidArgumentException("Invalid value");
        }

        $this->array[(string)$key] = $cleanValue;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $res = array();
        // Force to use the getters to fetch the values because they will unwrap the basic types
        foreach (array_keys($this->array) as $key) {
            $res[$key] = $this->get($key);
        }

        return $res;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->array)) {
            $res = $this->array[$key];
            if ($res instanceof Basic) {
                return $res->getValue();
            } elseif ($res instanceof EntityArray) {
                return $res->toArray();
            } else {
                return $res;
            }
        } else {
            return null;
        }
    }

}
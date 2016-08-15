<?php

namespace Smartbox\CoreBundle\Type;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Type\Traits\HasInternalType;

/**
 * Class SerializableArray.
 */
class SerializableArray implements SerializableInterface, \ArrayAccess
{
    use HasInternalType;

    /**
     * @var array
     * @JMS\Type("array<string,Smartbox\CoreBundle\Type\Entity>")
     * @JMS\Expose
     * @JMS\Groups({"logs"})
     * @JMS\XmlMap(inline = true)
     */
    protected $array = [];

    public function __construct($input = [])
    {
        if (is_array($input)) {
            $this->setArray($input);
        } else {
            $this->set(0, $input);
        }
    }

    public static function isSerializable($value)
    {
        $cleanValue = false;

        if (is_scalar($value)) {
            if (is_string($value)) {
                $cleanValue = new StringType($value);
            } elseif (is_int($value)) {
                $cleanValue = new IntegerType($value);
            } elseif (is_double($value)) {
                $cleanValue = new DoubleType($value);
            } elseif (is_bool($value)) {
                $cleanValue = new BooleanType($value);
            }
        } elseif (is_object($value) && $value instanceof \DateTime) {
            $cleanValue = new Date($value);
        } elseif (is_array($value) && count($value) > 0) {
            $cleanValue = new self($value);
        } elseif (is_object($value) && $value instanceof SerializableInterface) {
            $cleanValue = $value;
        }

        return !$value || $cleanValue !== false;
    }

    /**
     * @param string                                          $key
     * @param SerializableInterface|\DateTime|bool|int|string $value
     */
    public function set($key, $value)
    {
        if (!is_string($key) && !is_numeric($key)) {
            throw new \InvalidArgumentException('Invalid key');
        }

        $cleanValue = false;

        if (is_scalar($value)) {
            if (is_string($value)) {
                $cleanValue = new StringType($value);
            } elseif (is_int($value)) {
                $cleanValue = new IntegerType($value);
            } elseif (is_double($value)) {
                $cleanValue = new DoubleType($value);
            } elseif (is_bool($value)) {
                $cleanValue = new BooleanType($value);
            }
        } elseif (is_object($value) && $value instanceof \DateTime) {
            $cleanValue = new Date($value);
        } elseif (is_array($value) && count($value) > 0) {
            $cleanValue = new self($value);
        } elseif (is_object($value) && $value instanceof SerializableInterface) {
            $cleanValue = $value;
        }

        if ($cleanValue === false) {
            if ($value) {
                throw new \InvalidArgumentException('Invalid value');
            } else {
                $this->array[(string) $key] = null;
            }
        } else {
            $this->array[(string) $key] = $cleanValue;
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $res = [];
        // Force to use the getters to fetch the values because they will unwrap the basic types
        foreach (array_keys($this->array) as $key) {
            $res[$key] = $this->get($key);
        }

        return $res;
    }

    /**
     * @param string $key
     *
     * @return array|null
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->array)) {
            $res = $this->array[$key];
            if ($res instanceof Basic) {
                return $res->getValue();
            } elseif ($res instanceof self) {
                return $res->toArray();
            } else {
                return $res;
            }
        } else {
            return;
        }
    }

    /**
     * @param $array
     */
    public function setArray($array)
    {
        if (!is_array($array)) {
            throw new \InvalidArgumentException('Expected array');
        }

        foreach ($array as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return @$this->array[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }
}

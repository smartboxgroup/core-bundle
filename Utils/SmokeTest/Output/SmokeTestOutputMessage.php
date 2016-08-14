<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest\Output;

final class SmokeTestOutputMessage
{
    const OUTPUT_MESSAGE_TYPE_INFO = 'info';
    const OUTPUT_MESSAGE_TYPE_SUCCESS = 'success';
    const OUTPUT_MESSAGE_TYPE_FAILURE = 'failure';
    const OUTPUT_MESSAGE_TYPE_SKIPPED = 'skipped';

    /**
     * @var array
     */
    protected static $outputMessageTypes = [
        self::OUTPUT_MESSAGE_TYPE_INFO,
        self::OUTPUT_MESSAGE_TYPE_SUCCESS,
        self::OUTPUT_MESSAGE_TYPE_FAILURE,
    ];

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $type Type of the message. Supported types: info, success, failure
     * @param string $value The message value
     */
    public function __construct($type, $value)
    {
        // checking type
        if (!in_array($type, self::$outputMessageTypes)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unsupported message type. Given "%s", expected one of [%s].',
                    $type,
                    implode(', ', self::$outputMessageTypes)
                )
            );
        }

        // checking value
        if (!is_string($value)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Value of the message should be a string. Given "%s".',
                    gettype($value)
                )
            );
        }

        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('[%s] %s', $this->getType(), $this->getValue());
    }
}

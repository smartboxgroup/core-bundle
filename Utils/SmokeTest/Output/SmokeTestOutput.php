<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest\Output;

class SmokeTestOutput implements SmokeTestOutputInterface
{
    const OUTPUT_CODE_SUCCESS = 0;
    const OUTPUT_CODE_FAILURE = 1;

    /**
     * @var array
     */
    private static $outputCodes = [
        self::OUTPUT_CODE_SUCCESS,
        self::OUTPUT_CODE_FAILURE,
    ];

    private $outputCode;

    /**
     * @var SmokeTestOutputMessage[]
     */
    private $outputMessages = [];

    public function setCode($outputCode)
    {
        if (!\in_array($outputCode, self::$outputCodes)) {
            throw new \RuntimeException(
                \sprintf(
                    'Given argument is not supported: "%s". Provide one of: %s',
                    $outputCode,
                    \implode(', ', self::$outputCodes)
                )
            );
        }

        $this->outputCode = $outputCode;
    }

    public function isOK()
    {
        return self::OUTPUT_CODE_SUCCESS === $this->outputCode;
    }

    public function addInfoMessage($outputMessage)
    {
        $this->addMessage(
            new SmokeTestOutputMessage(SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_INFO, $outputMessage)
        );
    }

    public function addSuccessMessage($outputMessage)
    {
        $this->addMessage(
            new SmokeTestOutputMessage(SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_SUCCESS, $outputMessage)
        );
    }

    public function addFailureMessage($outputMessage)
    {
        $this->addMessage(
            new SmokeTestOutputMessage(SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_FAILURE, $outputMessage)
        );
    }

    /**
     * @param SmokeTestOutputMessage $message
     */
    public function addMessage(SmokeTestOutputMessage $message)
    {
        $this->outputMessages[] = $message;
    }

    /**
     * @return SmokeTestOutputMessage[]
     */
    public function getMessages()
    {
        return $this->outputMessages;
    }
}

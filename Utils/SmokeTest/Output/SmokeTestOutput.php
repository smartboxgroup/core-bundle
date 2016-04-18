<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest\Output;

class SmokeTestOutput implements SmokeTestOutputInterface
{
    const OUTPUT_CODE_SUCCESS = 0;
    const OUTPUT_CODE_FAILURE = 1;

    private static $outputCodes = [self::OUTPUT_CODE_SUCCESS, self::OUTPUT_CODE_FAILURE];

    private $outputCode;

    private $outputMessages = [];

    public function setCode($outputCode)
    {
        if (!in_array($outputCode, self::$outputCodes)) {
            throw new \RuntimeException(sprintf('Given argument is not supported: "%s". Provide one of: %s', $outputCode, implode(', ', self::$outputCodes)));
        }

        $this->outputCode = $outputCode;
    }

    public function isOK()
    {
        return $this->outputCode === self::OUTPUT_CODE_SUCCESS;
    }

    public function addMessage($outputMessage)
    {
        $this->outputMessages[] = $outputMessage;
    }

    public function getMessages()
    {
        return $this->outputMessages;
    }
}

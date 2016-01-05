<?php

namespace Smartbox\CoreBundle\Tests\Utils\Cache;

class FakeCacheServiceSpy
{
    private $log = [];

    public function notify($method, $arguments = [], $result = null)
    {
        $this->log[] = [
            'method' => $method,
            'arguments' => $arguments,
            'result' => $result,
        ];
    }

    public function getLog()
    {
        return $this->log;
    }
}
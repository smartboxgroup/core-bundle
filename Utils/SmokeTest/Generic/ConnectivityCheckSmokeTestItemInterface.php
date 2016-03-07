<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest\Generic;

use Smartbox\CoreBundle\Utils\SmokeTest\Output\SmokeTestOutputInterface;

interface ConnectivityCheckSmokeTestItemInterface
{
    /**
     * @param array|null $config
     * @return SmokeTestOutputInterface
     */
    public function checkConnectivityForSmokeTest(array $config = null);
}
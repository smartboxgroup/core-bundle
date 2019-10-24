<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest;

use Smartbox\CoreBundle\Utils\SmokeTest\Output\SmokeTestOutputInterface;

interface SmokeTestInterface
{
    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return SmokeTestOutputInterface
     */
    public function run();
}

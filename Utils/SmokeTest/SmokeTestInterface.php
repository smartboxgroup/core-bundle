<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest;

use Smartbox\CoreBundle\Utils\SmokeTest\Output\SmokeTestOutputInterface;

interface SmokeTestInterface
{
    /**
     * The label defines the critical level for smoke tests.
     * By default smoke-tests skips the WIP label
     */
    const SMOKE_TEST_LABEL_EMPTY = '';
    const SMOKE_TEST_LABEL_WIP = 'wip';
    const SMOKE_TEST_LABEL_IMPORTANT = 'important';
    const SMOKE_TEST_LABEL_CRITICAL = 'critical';
    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return SmokeTestOutputInterface
     */
    public function run();
}

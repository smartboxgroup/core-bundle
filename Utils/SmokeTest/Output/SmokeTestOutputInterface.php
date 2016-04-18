<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest\Output;

interface SmokeTestOutputInterface
{
    /**
     * @return bool
     */
    public function isOK();

    /**
     * @return array
     */
    public function getMessages();
}

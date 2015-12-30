<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest\Output;

interface SmokeTestOutputInterface
{
    /**
     * @return boolean
     */
    public function isOK();

    /**
     * @return array
     */
    public function getMessages();
}
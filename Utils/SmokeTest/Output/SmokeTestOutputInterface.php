<?php

namespace Smartbox\CoreBundle\Utils\SmokeTest\Output;

interface SmokeTestOutputInterface
{
    /**
     * @return bool
     */
    public function isOK();

    /**
     * @return SmokeTestOutputMessage[]
     */
    public function getMessages();
}

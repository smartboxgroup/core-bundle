<?php

namespace Smartbox\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseKernelTestCase extends KernelTestCase
{
    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return self::$kernel->getContainer();
    }

    protected function setUp()
    {
        $this->bootKernel();
    }
}

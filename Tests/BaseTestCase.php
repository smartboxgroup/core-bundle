<?php


namespace Smartbox\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

class BaseTestCase extends KernelTestCase
{
    /** @var  Kernel */
    protected static $kernel;

    protected static function getKernelClass()
    {
        return \AppKernel::class;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return self::$kernel->getContainer();
    }

    public function setUp()
    {
        $this->bootKernel();
    }

}
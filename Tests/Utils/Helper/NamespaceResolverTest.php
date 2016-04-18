<?php

namespace Smartbox\CoreBundle\Tests\Utils\Helper;

use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestComplexEntity;
use Smartbox\CoreBundle\Utils\Helper\NamespaceResolver;

/**
 * @coversDefaultClass Smartbox\CoreBundle\Utils\Helper\NamespaceResolver
 */
class NamespaceResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::resolveNamespaceForClass
     */
    public function testResolveNamespaceForClassNameOnly()
    {
        $namespaces = [
            'Fake\Namespace',
            'Smartbox\CoreBundle\Tests\Fixtures\Entity',
            'Another\Fake\Namespace',
        ];
        $namespaceResolver = new NamespaceResolver($namespaces);

        $this->assertEquals(TestComplexEntity::class, $namespaceResolver->resolveNamespaceForClass('TestComplexEntity'));
    }

    /**
     * @covers ::resolveNamespaceForClass
     */
    public function testResolveNamespaceForClassNamespace()
    {
        $namespaces = [
            'Fake\Namespace',
            'Smartbox\CoreBundle\Tests\Fixtures\Entity',
            'Another\Fake\Namespace',
        ];
        $namespaceResolver = new NamespaceResolver($namespaces);

        $this->assertEquals(TestComplexEntity::class, $namespaceResolver->resolveNamespaceForClass(TestComplexEntity::class));
    }

    /**
     * @covers ::resolveNamespaceForClass
     */
    public function testResolveNamespaceForNotExistingClass()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $namespaces = [
            'Fake\Namespace',
            'Another\Fake\Namespace',
        ];
        $namespaceResolver = new NamespaceResolver($namespaces);

        $namespaceResolver->resolveNamespaceForClass('NotExistingClass');
    }
}

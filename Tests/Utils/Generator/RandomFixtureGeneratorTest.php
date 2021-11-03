<?php

namespace Smartbox\CoreBundle\Tests\Utils\Generator;

use Smartbox\CoreBundle\Type\Context\ContextFactory;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestComplexEntity;
use Smartbox\CoreBundle\Utils\Generator\RandomFixtureGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Smartbox\CoreBundle\Utils\Generator\RandomFixtureGenerator
 */
class RandomFixtureGeneratorTest extends KernelTestCase
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var RandomFixtureGenerator
     */
    protected $randomFixtureGenerator;

    protected function setUp()
    {
        self::bootKernel();

        $this->application = new Application(self::$kernel);

        $this->randomFixtureGenerator = self::$container->get('smartcore.generator.random_fixture');
    }

    public static function getKernelClass()
    {
        return \AppKernel::class;
    }

    public function dataProviderForGroupsAndVersions()
    {
        return [
            [null, null],
            [null, EntityConstants::VERSION_1],
            [EntityConstants::GROUP_DEFAULT, null],
            [EntityConstants::GROUP_A, EntityConstants::VERSION_1],
            [EntityConstants::GROUP_B, EntityConstants::VERSION_2],
        ];
    }

    /**
     * @dataProvider dataProviderForGroupsAndVersions
     *
     * @param $group
     * @param $version
     *
     * @throws \Exception
     */
    public function testGenerate($group, $version)
    {
        $serializer = self::$container->get('jms_serializer');

        $entity = $this->randomFixtureGenerator->generate(TestComplexEntity::class, $group, $version);
        $serializedEntity = $serializer->serialize(
            $entity,
            'json',
            ContextFactory::createSerializationContextForFixtures($group, $version)
        );

        $deserializedEntity = $serializer->deserialize(
            $serializedEntity,
            TestComplexEntity::class,
            'json',
            ContextFactory::createDeserializationContextForFixtures($group, $version)
        );

        $this->assertEquals(TestComplexEntity::class, \get_class($entity));
        $this->assertEquals($entity, $deserializedEntity);
    }

    public function testGenerateForNonEntityClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->randomFixtureGenerator->generate(\stdClass::class, 'dummy_group', 'dummy_version');
    }
}

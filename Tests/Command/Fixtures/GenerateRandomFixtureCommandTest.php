<?php

namespace Smartbox\CoreBundle\Tests\Command\Fixtures;

use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Command\Fixtures\GenerateRandomFixtureCommand;
use Smartbox\CoreBundle\Type\Context\ContextFactory;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestComplexEntity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @group legacy
 *
 * @coversDefaultClass \Smartbox\CoreBundle\Command\Fixtures\GenerateRandomFixtureCommand
 */
class GenerateRandomFixtureCommandTest extends KernelTestCase
{
    /**
     * @var Application
     */
    protected $application;

    protected function setUp()
    {
        self::bootKernel();

        $this->application = new Application(self::$kernel);
    }

    public static function getKernelClass()
    {
        return \Smartbox\CoreBundle\Tests\AppKernel::class;
    }

    public function dataProviderForEntityGeneration()
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
     * @dataProvider dataProviderForEntityGeneration
     *
     * @param $group
     * @param $version
     */
    public function testExecute($group, $version)
    {
        $command = $this->application->find('smartbox:core:generate:random-fixture');
        $commandTester = new CommandTester($command);

        /** @var SerializerInterface $serializer */
        $serializer = self::$container->get('jms_serializer');

        $commandConfiguration = [];
        if (!\is_null($group)) {
            $commandConfiguration['--entity-group'] = $group;
        }
        if (!\is_null($version)) {
            $commandConfiguration['--entity-version'] = $version;
        }

        $commandConfiguration['--raw-output'] = true;

        $commandTester->execute(
            \array_merge(
                [
                    'command' => $command->getName(),
                    'entity' => 'TestComplexEntity',
                ],
                $commandConfiguration
            )
        );

        $this->assertTrue(
            \in_array($commandTester->getStatusCode(), [0, null], true),
            'Command should return proper status code.'
        );
        $this->assertInstanceOf(
            TestComplexEntity::class,
            @$serializer->deserialize(
                $commandTester->getDisplay(),
                TestComplexEntity::class,
                'json',
                ContextFactory::createDeserializationContextForFixtures($group, $version)
            ),
            'Generated entity should be instance of '.TestComplexEntity::class
        );
    }
}

<?php

namespace Smartbox\CoreBundle\Command\Fixtures;

use Smartbox\CoreBundle\Type\Context\ContextFactory;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRandomFixtureCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'smartbox:core:generate:random-fixture';

    /** @var InputInterface */
    protected $in;

    /** @var OutputInterface */
    protected $out;

    protected $version;

    protected $group;

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates an random fixture of a smartesb entity')
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity class')
            ->addOption(
                'entity-version',
                null,
                InputOption::VALUE_REQUIRED,
                'Determine the version of entity'
            )
            ->addOption(
                'entity-group',
                null,
                InputOption::VALUE_REQUIRED,
                'Determine a group of entity'
            )
            ->addOption(
                'raw-output',
                null,
                InputOption::VALUE_NONE,
                'If set raw json without comments will be printed'
            )
        ;
    }

    /**
     * @param InputInterface $in
     * @param OutputInterface $out
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $this->in = $in;
        $this->out = $out;
        $serializer = $this->getContainer()->get('serializer');
        $namespaceResolver = $this->getContainer()->get('smartcore.helper.entity_namespace_resolver');
        $entityClass = $this->in->getArgument('entity');
        $group = $this->in->getOption('entity-group');
        $version = $this->in->getOption('entity-version');
        $rawOutput = $this->in->getOption('raw-output');

        if (!$rawOutput) {
            $this->out->writeln("<info>####################################</info>");
            $this->out->writeln("<info>##    Random Fixture generator    ##</info>");
            $this->out->writeln("<info>####################################</info>");
            $this->out->writeln("");
        }

        $entityNamespace = $namespaceResolver->resolveNamespaceForClass($entityClass);

        $randomFixtureGenerator = $this->getContainer()->get('smartcore.generator.random_fixture');
        $entity = $randomFixtureGenerator->generate($entityNamespace, $group, $version);
        $context = ContextFactory::createSerializationContextForFixtures($group, $version);

        $result = $serializer->serialize($entity, 'json', $context);
        if (!$rawOutput) {
            $this->out->writeln("");
            $this->out->writeln("<info>Random fixture successfully generated.</info>");
            $this->out->writeln("");
        }

        $this->out->writeln($result);
    }
}
<?php

namespace Smartbox\CoreBundle\Command\Fixtures;

use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Type\Context\ContextFactory;
use Smartbox\CoreBundle\Utils\Generator\RandomFixtureGenerator;
use Smartbox\CoreBundle\Utils\Helper\NamespaceResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRandomFixtureCommand extends Command
{
    /** @var InputInterface */
    protected $in;

    /** @var OutputInterface */
    protected $out;

    protected $version;

    protected $group;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var NamespaceResolver
     */
    protected $namespaceResolver;

    /**
     * @var RandomFixtureGenerator
     */
    private $randomFixtureGenerator;

    public function __construct(string $name, SerializerInterface $serializer, NamespaceResolver $namespaceResolver, RandomFixtureGenerator $randomFixtureGenerator)
    {
        $this->serializer = $serializer;
        $this->namespaceResolver = $namespaceResolver;
        $this->randomFixtureGenerator = $randomFixtureGenerator;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
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
            );
    }

    /**
     * @param InputInterface  $in
     * @param OutputInterface $out
     *
     * @throws \InvalidArgumentException|\Exception
     */
    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $this->in = $in;
        $this->out = $out;
        $serializer = $this->serializer;
        $namespaceResolver = $this->namespaceResolver;
        $entityClass = $this->in->getArgument('entity');
        $group = $this->in->getOption('entity-group');
        $version = $this->in->getOption('entity-version');
        $rawOutput = $this->in->getOption('raw-output');

        if (!$rawOutput) {
            $this->out->writeln('<info>####################################</info>');
            $this->out->writeln('<info>##    Random Fixture generator    ##</info>');
            $this->out->writeln('<info>####################################</info>');
            $this->out->writeln('');
        }

        $entityNamespace = $namespaceResolver->resolveNamespaceForClass($entityClass);

        $randomFixtureGenerator = $this->randomFixtureGenerator;
        $entity = $randomFixtureGenerator->generate($entityNamespace, $group, $version);
        $context = ContextFactory::createSerializationContextForFixtures($group, $version);

        $result = $serializer->serialize($entity, 'json', $context);
        if (!$rawOutput) {
            $this->out->writeln('');
            $this->out->writeln('<info>Random fixture successfully generated.</info>');
            $this->out->writeln('');
        }

        $this->out->writeln($result);
    }
}
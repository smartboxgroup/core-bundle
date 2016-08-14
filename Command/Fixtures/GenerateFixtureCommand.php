<?php

namespace Smartbox\CoreBundle\Command\Fixtures;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Smartbox\CoreBundle\Type\Context\ContextFactory;
use Smartbox\CoreBundle\Type\Entity;
use Smartbox\CoreBundle\Type\EntityInterface;
use Smartbox\CoreBundle\Utils\Helper\NamespaceResolver;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class GenerateFixtureCommand extends ContainerAwareCommand
{
    /** @var  InputInterface */
    protected $in;

    /** @var  OutputInterface */
    protected $out;

    protected $version;

    protected $path;

    /** @var NamespaceResolver */
    protected $namespaceResolver;

    protected function configure()
    {
        $this
            ->setName('smartbox:generate:fixture')
            ->setAliases(array('generate:smartbox:fixture'))
            ->setDescription('Generates a fixture of a smartesb entity serialized in json');
    }

    public function getCleanDefaultPathForFixture($name)
    {
        $folder = realpath($this->getContainer()->getParameter('smartcore.fixtures_path'));

        if (!file_exists($folder)) {
            throw new \RuntimeException("Fixtures folder $folder doesn't exist");
        }

        if (!is_dir($folder)) {
            throw new \RuntimeException("Fixtures folder $folder is not a directory");
        }

        if (strpos($name, '/') !== false) {
            $parts = explode('/', $name);
            array_pop($parts);

            $dir = $folder . '/' . implode('/', $parts);
            if (!file_exists($dir)) {
                mkdir($dir, 777, true);
            }
        }

        $path = $folder . "/$name.json";

        return $path;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $this->in = $in;
        $this->out = $out;
        $this->namespaceResolver = $this->getContainer()->get('smartcore.helper.entity_namespace_resolver');

        $this->out->writeln('<info>###################################</info>');
        $this->out->writeln('<info>##       Fixture generator       ##</info>');
        $this->out->writeln('<info>###################################</info>');
        $this->out->writeln('');

        $name = $this->ask('Name for the fixture: ');
        $this->version = $this->ask('Model verson number (v1): ', 'v1');
        $entity = $this->askForEntity('this fixture', null);

        $path = $this->getCleanDefaultPathForFixture($name);

        $context = ContextFactory::createSerializationContextForFixtures($entity->getEntityGroup(), $entity->getAPIVersion());

        $result = $this->getContainer()->get('serializer')->serialize($entity, 'json', $context);

        file_put_contents($path, $result);

        $this->out->writeln('');
        $this->out->writeln("<info>Fixture successfully generated in $path.</info>");
    }

    /**
     * @param $class
     *
     * @return EntityInterface
     */
    protected function askForEntity($field, $class)
    {
        if (!$class) {
            $question = "Entity class for $field: ";
            $class = $this->ask($question);
            $class = $this->namespaceResolver->resolveNamespaceForClass($class);

            while (!(is_string($class) && class_exists($class) && is_subclass_of($class, EntityInterface::class))) {
                $this->out->writeln('<error>Invalid entity class.</error>');
                $class = $this->ask($question);
            }
        }

        $context = new SerializationContext();
        $context->setVersion($this->version);
        $versionExclusion = new VersionExclusionStrategy($this->version);

        $group = $this->ask('Group for this entity(none): ', null);
        if ($group) {
            $groupExclusion = new GroupsExclusionStrategy(array($group));
            $context->setGroups(array($group));
        }

        /** @var EntityInterface $entity */
        $entity = new $class();
        $entity->setAPIVersion($this->version);
        $entity->setEntityGroup($group);

        $propertyAccessor = new PropertyAccessor();
        $metadata = $this->getEntityMetadata($class);

        foreach ($metadata->propertyMetadata as $property => $propertyMetadata) {
            if (!in_array($property, array('_group', '_apiVersion', '_type'))
                && $propertyAccessor->isWritable($entity, $property)
                && (!$group || !$groupExclusion->shouldSkipProperty($propertyMetadata, $context))
                && !$versionExclusion->shouldSkipProperty($propertyMetadata, $context)
            ) {
                $typeName = @$propertyMetadata->type['name'];
                $typeParams = @$propertyMetadata->type['params'];

                if (empty($typeName)) {
                    continue;
                }

                if ($typeName != 'array') {
                    $result = $this->askForField('Value for ' . $property, $typeName);
                } else {
                    $numParams = count($typeParams);

                    if ($numParams == 2) {
                        $keyType = @$typeParams[0]['name'];
                        $valueType = $typeParams[1]['name'];
                    } elseif ($numParams == 1) {
                        $keyType = null;
                        $valueType = $typeParams[0]['name'];
                    } else {
                        throw new \RuntimeException('Missing jms type params');
                    }

                    $question = "Do you want to add a new entry to $property? (yes): ";

                    $result = array();

                    while ($this->ask($question, 'yes') == 'yes') {
                        $key = null;

                        if ($keyType) {
                            $key = $this->askForField($property . '[] Key', $keyType);
                        }

                        $value = $this->askForField($property . '[] Value', $valueType);

                        if ($key) {
                            $result[$key] = $value;
                        } else {
                            $result[] = $value;
                        }
                    }
                }

                $propertyAccessor->setValue($entity, $property, $result);
            }
        }

        return $entity;
    }

    protected function ask($text, $default = null)
    {
        $helper = $this->getQuestionHelper();
        $question = new Question($text, $default);
        $res = $helper->ask($this->in, $this->out, $question);
        $this->out->writeln('');

        return $res;
    }

    protected function askForField($field, $tName)
    {
        $question = "$field ($tName): ";
        $result = null;

        switch ($tName) {
            case 'DateTime':
                $customQuestion = "$field (Datetime, e.g.: '2011-05-24 10:20'): ";
                $result = $this->ask($customQuestion);
                try {
                    $result = new \DateTime($result);
                } catch (\Exception $ex) {
                    $result = 'INVALID';
                }

                while ($result == 'INVALID') {
                    $this->out->writeln('<error>Invalid date. Use the format Year-Month-Day Hours:Minutes.</error>');
                    $result = $this->ask($customQuestion);
                }

                break;

            case 'integer':
                $result = $this->ask($question);
                while (!(is_numeric($result) || empty($result))) {
                    $this->out->writeln('<error>Invalid integer.</error>');
                    $result = $this->ask($question);
                }

                if ($result) {
                    $result = intval($result);
                }

                break;

            case 'double':
                $result = $this->ask($question);
                while (!is_numeric($result) || empty($result)) {
                    $this->out->writeln('<error>Invalid double.</error>');
                    $result = $this->ask($question);
                }

                if ($result) {
                    $result = doubleval($result);
                }

                break;

            case 'string':
                $result = $this->ask($question);
                break;

            case 'boolean':
                $result = $this->ask($question);

                while ($result !== 'true' && $result !== 'false') {
                    $this->out->writeln('<error>Invalid boolean.</error>');
                    $result = $this->ask($question);
                }

                $result = ($result === 'true') ? true : false;

                break;

            default:
                if (is_string($tName) && class_exists($tName) && is_a($tName, EntityInterface::class, true)) {
                    if ($tName == EntityInterface::class) {
                        $tName = null;
                    }

                    $skip = $this->ask("$field (entity); do you want to leave this field blank? (yes): ", 'yes') == 'yes';

                    if ($skip) {
                        return;
                    }

                    $useExisting = $this->ask("$field (entity); do you want to use an existing entity fixture? (yes): ", 'yes') == 'yes';

                    if (!$useExisting) {
                        $result = $this->askForEntity($field, $tName);
                    } else {
                        $askForName = "$field (entity); fixture name: ";

                        while (!$result) {
                            $name = $this->ask($askForName);

                            while (!file_exists($path = $this->getCleanDefaultPathForFixture($name))) {
                                $this->out->writeln("<error>Fixture not found in: $path</error>");
                                $name = $this->ask($askForName);
                            }

                            $serializer = $this->getContainer()->get('serializer');
                            $obj = $serializer->deserialize(file_get_contents($path), Entity::class, 'json');
                            if (!$obj || ($tName && !(is_a($obj, $tName) || is_subclass_of($obj, $tName)))) {
                                $this->out->writeln("<error>The given fixture is not of type: $tName but of type " . $obj->getType() . '</error>');
                            } else {
                                $result = $obj;
                            }
                        }
                    }
                } else {
                    throw new RuntimeException("Unrecognized type: $tName");
                }
        }

        return $result;
    }

    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)',
                    $entity
                )
            );
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }

    protected function getEntityMetadata($entity)
    {
        $factory = $this->getContainer()->get('jms_serializer.metadata_factory');

        return $factory->getMetadataForClass($entity);
    }

    protected function getQuestionHelper()
    {
        $question = $this->getHelperSet()->get('question');
        if (!$question || get_class($question) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper') {
            $this->getHelperSet()->set($question = new QuestionHelper());
        }

        return $question;
    }
}

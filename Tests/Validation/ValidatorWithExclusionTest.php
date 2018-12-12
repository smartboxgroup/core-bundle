<?php

namespace Smartbox\CoreBundle\Tests\Services\Validation;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use Metadata\MetadataFactory;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;
use Smartbox\CoreBundle\Tests\Symfony\Validator\RecursiveValidator2Dot5ApiTest;
use Smartbox\CoreBundle\Validation\ValidatorWithExclusion;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class ValidatorWithExclusionTest extends RecursiveValidator2Dot5ApiTest
{
    /** @var ValidatorWithExclusion */
    protected $validator;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param array                    $objectInitializers
     *
     * @return ValidatorWithExclusion
     */
    protected function createValidator(MetadataFactoryInterface $metadataFactory, array $objectInitializers = [])
    {
        $translator = new IdentityTranslator();
        $translator->setLocale('en');

        $contextFactory = new ExecutionContextFactory($translator);
        $validatorFactory = new ConstraintValidatorFactory();

        $recursiveValidator = new RecursiveValidator(
            $contextFactory,
            $metadataFactory,
            $validatorFactory,
            $objectInitializers
        );

        $validator = new ValidatorWithExclusion();
        $validator->setDecoratedValidator($recursiveValidator);

        return $validator;
    }

    /**
     * @return array
     */
    public function exclusionCombinationsProvider()
    {
        // Group, Version, Errors
        return [
            [null, null, 3],
            [EntityConstants::GROUP_A, null, 3],
            [EntityConstants::GROUP_B, null, 2],
            ['XXX', null, 0],
            [null, EntityConstants::VERSION_2, 3],
            [null, EntityConstants::VERSION_1, 2],
            [EntityConstants::GROUP_A, EntityConstants::VERSION_2, 3],
            [EntityConstants::GROUP_A, EntityConstants::VERSION_1, 2],
        ];
    }

    /**
     * @dataProvider exclusionCombinationsProvider
     */
    public function testExclusion($group, $version, $expectedErrorCount)
    {
        $entity = new TestEntity();

        $metadata = new ClassMetadata(TestEntity::class);
        $metadata->addPropertyConstraint('title', new NotBlank());
        $metadata->addPropertyConstraint('description', new NotBlank());
        $metadata->addPropertyConstraint('note', new NotBlank());

        $driver = new AnnotationDriver(new AnnotationReader());
        $jmsMetadata = new MetadataFactory($driver);

        $this->validator->setMetadataFactory($jmsMetadata);
        $this->metadataFactory->addMetadata($metadata);

        $entity->setEntityGroup($group);
        $entity->setAPIVersion($version);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($expectedErrorCount, \count($errors));
    }
}

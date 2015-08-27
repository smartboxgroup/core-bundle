<?php

namespace Smartbox\ApiBundle\Tests\Services\Validation;


use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use Metadata\MetadataFactory;
use Smartbox\CoreBundle\Entity\Entity;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;
use Smartbox\CoreBundle\Validation\ValidatorWithExclusion;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\Tests\Validator\RecursiveValidator2Dot5ApiTest;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class ValidatorWithExclusionTest extends RecursiveValidator2Dot5ApiTest
{
    /** @var  ValidatorWithExclusion */
    protected $validator;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param array $objectInitializers
     * @return ValidatorWithExclusion
     */
    protected function createValidator(MetadataFactoryInterface $metadataFactory, array $objectInitializers = array())
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
    public function exclusionCombinationsProvider(){
        // Group, Version, Errors
        return array(
            array(null,null,3),
            array("A",null,2),
            array("B",null,1),
            array("XXX",null,0),
            array(null,"v2",3),
            array(null,"v1",2),
            array("A","v2",2),
            array("A","v1",1),
        );
    }

    /**
     * @dataProvider exclusionCombinationsProvider
     */
    public function testExclusion($group, $version, $expectedErrorCount){
        $entity = new TestEntity();

        $metadata = new ClassMetadata(TestEntity::class);
        $metadata->addPropertyConstraint('title',new NotBlank());
        $metadata->addPropertyConstraint('description',new NotBlank());
        $metadata->addPropertyConstraint('note',new NotBlank());

        $driver = new AnnotationDriver(new AnnotationReader());
        $jmsMetadata = new MetadataFactory($driver);

        $this->validator->setMetadataFactory($jmsMetadata);
        $this->metadataFactory->addMetadata($metadata);

        $entity->setGroup($group);
        $entity->setVersion($version);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($expectedErrorCount,count($errors));
    }
}
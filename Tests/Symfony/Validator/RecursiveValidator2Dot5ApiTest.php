<?php

/*
 * This file is copied and changed from
 *
 * Symfony\Component\Validator\Tests\Validator\RecursiveValidator2Dot5ApiTest
 *
 * to fix a deprecation notice that appears starting from PhpUnit 5.4 (using deprecated getMock)
 *
 * @todo Remove the file when symfony dependency is updated to version 3
 */

namespace Smartbox\CoreBundle\Tests\Symfony\Validator;

use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\Tests\Fixtures\Entity;
use Symfony\Component\Validator\Validator\RecursiveValidator;

abstract class RecursiveValidator2Dot5ApiTest extends Abstract2Dot5ApiTest
{
    protected function createValidator(MetadataFactoryInterface $metadataFactory, array $objectInitializers = [])
    {
        $translator = new IdentityTranslator();
        $translator->setLocale('en');

        $contextFactory = new ExecutionContextFactory($translator);
        $validatorFactory = new ConstraintValidatorFactory();

        return new RecursiveValidator($contextFactory, $metadataFactory, $validatorFactory, $objectInitializers);
    }

    public function testEmptyGroupsArrayDoesNotTriggerDeprecation()
    {
        $entity = new Entity();

        $validatorContext = $this->getMockBuilder('Symfony\Component\Validator\Validator\ContextualValidatorInterface')->getMock();
        $validatorContext
            ->expects($this->once())
            ->method('validate')
            ->with($entity, null, [])
            ->willReturnSelf();

        $validator = $this
            ->getMockBuilder('Symfony\Component\Validator\Validator\RecursiveValidator')
            ->disableOriginalConstructor()
            ->setMethods(['startContext'])
            ->getMock();
        $validator
            ->expects($this->once())
            ->method('startContext')
            ->willReturn($validatorContext);

        $validator->validate($entity, null, []);
    }
}

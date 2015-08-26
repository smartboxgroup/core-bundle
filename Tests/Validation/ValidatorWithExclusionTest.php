<?php

namespace Smartbox\ApiBundle\Tests\Services\Validation;


use Smartbox\CoreBundle\Validation\ValidatorWithExclusion;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\Tests\Validator\RecursiveValidator2Dot5ApiTest;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class ValidatorWithExclusionTest extends RecursiveValidator2Dot5ApiTest
{

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
}
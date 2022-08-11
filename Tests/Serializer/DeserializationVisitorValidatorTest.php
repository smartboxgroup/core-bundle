<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use JMS\Serializer\DeserializationContext;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException;
use Smartbox\CoreBundle\Serializer\DeserializationTypesValidator;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker;
use JMS\Serializer\Context;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * @group deserialization
 */
class DeserializationVisitorValidatorTest extends TestCase
{
    /** @var DeserializationTypesValidator | MockObject */
    private object $visitorValidator;

    /** @var PropertyNamingStrategyInterface|MockObject */
    private object $namingStrategy;

    /** @var StrongDeserializationCastingChecker|MockObject */
    private object $castingChecker;

    /** @var Context|MockObject */
    private object $context;

    /** @var ExclusionStrategyInterface|MockObject */
    private object $exclusionStrategy;

    /** @var \SplStack|MockObject */
    private object $metadataStack;

    /** @var PropertyMetadata|MockObject */
    private object $currentPropertyMetadata;

    protected function setUp(): void
    {
        $this->namingStrategy = $this->getMockBuilder(PropertyNamingStrategyInterface::class)
            ->getMock();
        $this->castingChecker = $this->getMockBuilder(
            StrongDeserializationCastingChecker::class
        )->getMock();

        $this->context = $this->getMockBuilder(Context::class)
            ->getMock();

        $this->exclusionStrategy = $this->getMockBuilder(ExclusionStrategyInterface::class)
            ->getMock();

        $this->metadataStack = $this->getMockBuilder(\SplStack::class)->getMock();
        $this->currentPropertyMetadata = $this->getMockBuilder(PropertyMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadataStack->method('top')->willReturn($this->currentPropertyMetadata);

        $this->context->method('getMetadataStack')->willReturn($this->metadataStack);
        $this->context->method('getExclusionStrategy')->willReturn($this->exclusionStrategy);

        $this->visitorMock = $this
            ->getMockBuilder(DeserializationContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->visitorValidator = new DeserializationTypesValidator($this->castingChecker);
    }

    public function testItShouldNotCheckAnExcludedString(): void
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(true);

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToString');

        $this->visitorValidator->validateString('some string', $this->context);
    }

    public function testItShouldCheckAValidString(): void
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(false);

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToString')
            ->willReturn(true);

        $this->visitorValidator->validateString('some string', $this->context);
    }

    public function testItShouldRaiseAnExceptionWhenVisitingAnInvalidString(): void
    {
        $this->expectException(DeserializationTypeMismatchException::class);

        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(false);

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToString')
            ->willReturn(false);

        $this->visitorValidator->validateString(11111, $this->context);
    }

    public function testItShouldNotCheckAnExcludedBoolean(): void
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(true);

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToBoolean');

        $this->visitorValidator->validateBoolean(true, $this->context);
    }

    public function testItShouldCheckAValidBoolean(): void
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(false);

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToBoolean')
            ->willReturn(true);

        $this->visitorValidator->validateBoolean(true, $this->context);
    }

    public function testItShouldRaiseAnExceptionWhenVisitingAnInvalidBoolean(): void
    {
        $this->expectException(DeserializationTypeMismatchException::class);

        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(false);

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToBoolean')
            ->willReturn(false);

        $this->visitorValidator->validateBoolean(11111, $this->context);
    }

    public function testItShouldNotCheckAnExcludedInteger(): void
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(true);

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToInteger');

        $this->visitorValidator->validateInteger(17, $this->context);
    }

    public function testItShouldCheckAValidInteger(): void
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(false);

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToInteger')
            ->willReturn(true);

        $this->visitorValidator->validateInteger(17, $this->context);
    }

    public function testItShouldRaiseAnExceptionWhenVisitingAnInvalidInteger(): void
    {
        $this->expectException(DeserializationTypeMismatchException::class);

        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(false);

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToInteger')
            ->willReturn(false);

        $this->visitorValidator->validateInteger(
            'notAnInteger',
            $this->context
        );
    }

    public function testItShouldNotCheckAnExcludedDouble(): void
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(true);

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToDouble');

        $this->visitorValidator->validateDouble(22.5, $this->context);
    }

    public function testItShouldCheckAValidDouble(): void
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(false);

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToDouble')
            ->willReturn(true);

        $this->visitorValidator->validateDouble(22.4, $this->context);
    }

    public function testItShouldRaiseAnExceptionWhenVisitingAnInvalidDouble(): void
    {
        $this->expectException(DeserializationTypeMismatchException::class);

        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->willReturn(false);

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToDouble')
            ->willReturn(false);

        $this->visitorValidator->validateDouble('notADouble', $this->context);
    }
}
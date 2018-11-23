<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use JMS\Serializer\GenericDeserializationVisitor;
use Smartbox\CoreBundle\Serializer\DeserializationTypesValidator;

class DeserializationVisitorValidatorTest extends \PHPUnit\Framework\TestCase
{
    /** @var \JMS\Serializer\GenericDeserializationVisitor|\PHPUnit_Framework_MockObject_MockObject */
    private $visitorMock;

    /** @var DeserializationTypesValidator|\PHPUnit_Framework_MockObject_MockObject */
    private $visitorValidator;

    /** @var \JMS\Serializer\Naming\PropertyNamingStrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $namingStrategy;

    /** @var \Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker|\PHPUnit_Framework_MockObject_MockObject */
    private $castingChecker;

    /** @var \JMS\Serializer\Context|\PHPUnit_Framework_MockObject_MockObject */
    private $context;

    /** @var \JMS\Serializer\Exclusion\ExclusionStrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $exclusionStrategy;

    /** @var \SplStack|\PHPUnit_Framework_MockObject_MockObject */
    private $metadataStack;

    /** @var \JMS\Serializer\Metadata\PropertyMetadata|\PHPUnit_Framework_MockObject_MockObject */
    private $currentPropertyMetadata;

    protected function setUp()
    {
        $this->namingStrategy = $this->getMockBuilder('\JMS\Serializer\Naming\PropertyNamingStrategyInterface')
            ->getMock();
        $this->castingChecker = $this->getMockBuilder(
            '\Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker'
        )->getMock();

        $this->context = $this->getMockBuilder('\JMS\Serializer\Context')
            ->getMock();

        $this->exclusionStrategy = $this->getMockBuilder('\JMS\Serializer\Exclusion\ExclusionStrategyInterface')
            ->getMock();

        $this->metadataStack = $this->getMockBuilder('\SplStack')->getMock();
        $this->currentPropertyMetadata = $this->getMockBuilder('\JMS\Serializer\Metadata\PropertyMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadataStack->method('top')->will($this->returnValue($this->currentPropertyMetadata));

        $this->context->method('getMetadataStack')->will($this->returnValue($this->metadataStack));
        $this->context->method('getExclusionStrategy')->willReturn($this->exclusionStrategy);

        $this->visitorMock = $this
            ->getMockBuilder(GenericDeserializationVisitor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->visitorValidator = new DeserializationTypesValidator($this->castingChecker);
    }

    public function testItShouldNotCheckAnExcludedString()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(true));

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToString');

        $this->visitorValidator->validateString('some string', $this->context, $this->visitorMock->getCurrentObject());
    }

    public function testItShouldCheckAValidString()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false));

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToString')
            ->will($this->returnValue(true));

        $this->visitorValidator->validateString('some string', $this->context, $this->visitorMock->getCurrentObject());
    }

    /**
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     */
    public function testItShouldRaiseAnExceptionWhenVisitingAnInvalidString()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false));

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToString')
            ->will($this->returnValue(false));

        $this->visitorValidator->validateString(11111, $this->context, $this->visitorMock->getCurrentObject());
    }

    public function testItShouldNotCheckAnExcludedBoolean()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(true));

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToBoolean');

        $this->visitorValidator->validateBoolean(true, $this->context, $this->visitorMock->getCurrentObject());
    }

    public function testItShouldCheckAValidBoolean()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false));

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToBoolean')
            ->will($this->returnValue(true));

        $this->visitorValidator->validateBoolean(true, $this->context, $this->visitorMock->getCurrentObject());
    }

    /**
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     */
    public function testItShouldRaiseAnExceptionWhenVisitingAnInvalidBoolean()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false));

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToBoolean')
            ->will($this->returnValue(false));

        $this->visitorValidator->validateBoolean(11111, $this->context, $this->visitorMock->getCurrentObject());
    }

    public function testItShouldNotCheckAnExcludedInteger()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(true));

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToInteger');

        $this->visitorValidator->validateInteger(17, $this->context, $this->visitorMock->getCurrentObject());
    }

    public function testItShouldCheckAValidInteger()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false));

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToInteger')
            ->will($this->returnValue(true));

        $this->visitorValidator->validateInteger(17, $this->context, $this->visitorMock->getCurrentObject());
    }

    /**
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     */
    public function testItShouldRaiseAnExceptionWhenVisitingAnInvalidInteger()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false));

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToInteger')
            ->will($this->returnValue(false));

        $this->visitorValidator->validateInteger(
            'notAnInteger',
            $this->context,
            $this->visitorMock->getCurrentObject()
        );
    }

    public function testItShouldNotCheckAnExcludedDouble()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(true));

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToDouble');

        $this->visitorValidator->validateDouble(22.5, $this->context, $this->visitorMock->getCurrentObject());
    }

    public function testItShouldCheckAValidDouble()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false));

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToDouble')
            ->will($this->returnValue(true));

        $this->visitorValidator->validateDouble(22.4, $this->context, $this->visitorMock->getCurrentObject());
    }

    /**
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     */
    public function testItShouldRaiseAnExceptionWhenVisitingAnInvalidDouble()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false));

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToDouble')
            ->will($this->returnValue(false));

        $this->visitorValidator->validateDouble('notADouble', $this->context, $this->visitorMock->getCurrentObject());
    }
}

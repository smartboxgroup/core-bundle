<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use Smartbox\CoreBundle\Tests\Fixtures\Serializer\CastingCheckerDeserializer;

class CastingCheckerAwareVisitorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Smartbox\CoreBundle\Serializer\CastingCheckerAwareVisitor */
    private $visitor;

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

    public function setup()
    {
        $this->namingStrategy = $this->getMockBuilder('\JMS\Serializer\Naming\PropertyNamingStrategyInterface')
            ->getMock();
        $this->castingChecker = $this->getMockBuilder('\Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker')
            ->getMock();

        $this->context = $this->getMockBuilder('\JMS\Serializer\Context')
            ->getMock();

        $this->exclusionStrategy = $this->getMockBuilder('\JMS\Serializer\Exclusion\ExclusionStrategyInterface')
            ->getMock();

        $this->metadataStack = $this->getMockBuilder('\SplStack')->getMock();
        $this->currentPropertyMetadata = $this->getMockBuilder('\JMS\Serializer\Metadata\PropertyMetadata')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->metadataStack->method('top')->will($this->returnValue($this->currentPropertyMetadata));
        $this->currentPropertyMetadata->method('getName')->will($this->returnValue('property'));
        $this->currentPropertyMetadata->method('getClass')->will($this->returnValue('className'));

        $this->context->method('getMetadataStack')->will($this->returnValue($this->metadataStack));
        $this->context->method('getExclusionStrategy')->willReturn($this->exclusionStrategy);

        $this->visitor = new CastingCheckerDeserializer($this->namingStrategy, $this->castingChecker, $this->metadataStack);
    }

    /**
     * @test
     */
    public function it_should_not_check_an_excluded_string()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(true))
        ;

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToString')
        ;

        $this->visitor->visitString('some string', [], $this->context);
    }

    /**
     * @test
     */
    public function it_should_check_a_valid_string()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false))
        ;

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToString')
            ->will($this->returnValue(true))
        ;

        $this->visitor->visitString('some string', [], $this->context);
    }

    /**
     * @test
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     */
    public function it_should_raise_an_exception_when_visiting_an_invalid_string()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false))
        ;

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToString')
            ->will($this->returnValue(false))
        ;

        $this->visitor->visitString(11111, [], $this->context);
    }

    /**
     * @test
     */
    public function it_should_not_check_an_excluded_boolean()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(true))
        ;

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToBoolean')
        ;

        $this->visitor->visitBoolean(true, [], $this->context);
    }

    /**
     * @test
     */
    public function it_should_check_a_valid_boolean()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false))
        ;

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToBoolean')
            ->will($this->returnValue(true))
        ;

        $this->visitor->visitBoolean(true, [], $this->context);
    }

    /**
     * @test
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     */
    public function it_should_raise_an_exception_when_visiting_an_invalid_boolean()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false))
        ;

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToBoolean')
            ->will($this->returnValue(false))
        ;

        $this->visitor->visitBoolean(11111, [], $this->context);
    }

    /**
     * @test
     */
    public function it_should_not_check_an_excluded_integer()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(true))
        ;

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToInteger')
        ;

        $this->visitor->visitInteger(17, [], $this->context);
    }

    /**
     * @test
     */
    public function it_should_check_a_valid_integer()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false))
        ;

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToInteger')
            ->will($this->returnValue(true))
        ;

        $this->visitor->visitInteger(17, [], $this->context);
    }

    /**
     * @test
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     */
    public function it_should_raise_an_exception_when_visiting_an_invalid_integer()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false))
        ;

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToInteger')
            ->will($this->returnValue(false))
        ;

        $this->visitor->visitInteger('notAnInteger', [], $this->context);
    }

    /**
     * @test
     */
    public function it_should_not_check_an_excluded_double()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(true))
        ;

        $this->castingChecker
            ->expects($this->never())
            ->method('canBeCastedToDouble')
        ;

        $this->visitor->visitDouble(22.5, [], $this->context);
    }

    /**
     * @test
     */
    public function it_should_check_a_valid_double()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false))
        ;

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToDouble')
            ->will($this->returnValue(true))
        ;

        $this->visitor->visitDouble(22.4, [], $this->context);
    }

    /**
     * @test
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     */
    public function it_should_raise_an_exception_when_visiting_an_invalid_double()
    {
        $this->exclusionStrategy
            ->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnValue(false))
        ;

        $this->castingChecker
            ->expects($this->once())
            ->method('canBeCastedToDouble')
            ->will($this->returnValue(false))
        ;

        $this->visitor->visitDouble('notADouble', [], $this->context);
    }
}

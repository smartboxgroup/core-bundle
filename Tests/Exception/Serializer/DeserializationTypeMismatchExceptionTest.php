<?php

namespace Smartbox\CoreBundle\Tests\Exception\Serializer;

use Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException;

class DeserializationTypeMismatchExceptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var DeserializationTypeMismatchException */
    private $exception;

    public function setup() {
        $this->exception = new DeserializationTypeMismatchException('some property', 'some class', 'some value', 'some type', 'original data');
    }

    /**
     * @test
     */
    public function it_should_get_property_name()
    {
        $this->assertEquals('some property', $this->exception->getPropertyName());
    }

    /**
     * @test
     */
    public function it_should_get_class_name()
    {
        $this->assertEquals('some class', $this->exception->getClassName());
    }

    /**
     * @test
     */
    public function it_should_get_property_value()
    {
        $this->assertEquals('some value', $this->exception->getPropertyValue());
    }

    /**
     * @test
     */
    public function it_should_get_expected_type()
    {
        $this->assertEquals('some type', $this->exception->getExpectedType());
    }

    /**
     * @test
     */
    public function it_should_get_original_data()
    {
        $this->assertEquals('original data', $this->exception->getOriginalData());
    }
}

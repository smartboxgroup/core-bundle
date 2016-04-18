<?php

namespace Smartbox\CoreBundle\Tests\Exception\Serializer;

use Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException;

class DeserializationTypeMismatchExceptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var DeserializationTypeMismatchException */
    private $exception;

    public function setup()
    {
        $this->exception = new DeserializationTypeMismatchException('some property', 'some class', 'some value', 'some type', 'original data');
    }

    public function testItShouldGetPropertyName()
    {
        $this->assertEquals('some property', $this->exception->getPropertyName());
    }

    public function testItShouldGetClassName()
    {
        $this->assertEquals('some class', $this->exception->getClassName());
    }

    public function testItShouldGetPropertyValue()
    {
        $this->assertEquals('some value', $this->exception->getPropertyValue());
    }

    public function testItShouldGetExpectedType()
    {
        $this->assertEquals('some type', $this->exception->getExpectedType());
    }

    public function testItShouldGetOriginalData()
    {
        $this->assertEquals('original data', $this->exception->getOriginalData());
    }
}

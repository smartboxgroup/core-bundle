<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker;

class StrongDeserializationCastingCheckerTest extends \PHPUnit_Framework_TestCase
{
    /** @var StrongDeserializationCastingChecker */
    private $checker;

    public function setup()
    {
        $this->checker = new StrongDeserializationCastingChecker();
    }

    public function validString() {
        return [
            [null],
            [''],
            ['some string'],
            ['123456'],
        ];
    }

    public function invalidString() {
        return [
            [123456],
            [22.1767],
            [0],
            [[]],
            [new \stdClass()],
        ];
    }

    public function validBoolean() {
        return [
            [null],
            [true],
            [false],
            ['true'],
            ['false'],
            [0],
            [1],
        ];
    }

    public function invalidBoolean() {
        return [
            ['foo'],
            [1234],
            [1.234],
            [[]],
            [new \stdClass()],
        ];
    }

    public function validInteger() {
        return [
            [null],
            [0],
            [1],
            [1234567],
            ['1234567'],
        ];
    }

    public function invalidInteger() {
        return [
            ['aaaa'],
            ['12aaaa'],
            [0.5],
            [[]],
            [new \stdClass()],
        ];
    }

    public function validDouble() {
        return [
            [null],
            [0],
            [1],
            [1234567],
            ['1234567'],
            [0.17],
            ['0.17'],
        ];
    }

    public function invalidDouble() {
        return [
            ['aaaa'],
            ['12aaaa'],
            [[]],
            [new \stdClass()],
        ];
    }

    /**
     * @test
     * @dataProvider validString
     * @param $string
     */
    public function it_should_check_a_valid_string($string) {
        $this->assertTrue($this->checker->canBeCastedToString($string));
    }

    /**
     * @test
     * @dataProvider invalidString
     * @param $string
     */
    public function it_should_check_an_invalid_string($string) {
        $this->assertFalse($this->checker->canBeCastedToString($string));
    }

    /**
     * @test
     * @dataProvider validBoolean
     * @param $bool
     */
    public function it_should_check_a_valid_boolean($bool) {
        $this->assertTrue($this->checker->canBeCastedToBoolean($bool));
    }

    /**
     * @test
     * @dataProvider invalidBoolean
     * @param $bool
     */
    public function is_should_check_an_invalid_boolean($bool) {
        $this->assertFalse($this->checker->canBeCastedToBoolean($bool));
    }

    /**
     * @test
     * @dataProvider validInteger
     * @param $int
     */
    public function it_should_check_a_valid_integer($int) {
        $this->assertTrue($this->checker->canBeCastedToInteger($int));
    }

    /**
     * @test
     * @dataProvider invalidInteger
     * @param $int
     */
    public function it_should_check_an_invalid_integer($int) {
        $this->assertFalse($this->checker->canBeCastedToInteger($int));
    }

    /**
     * @test
     * @dataProvider validDouble
     * @param $double
     */
    public function it_should_check_a_valid_double($double) {
        $this->assertTrue($this->checker->canBeCastedToDouble($double));
    }

    /**
     * @test
     * @dataProvider invalidDouble
     * @param $double
     */
    public function it_should_check_an_invalid_double($double) {
        $this->assertFalse($this->checker->canBeCastedToDouble($double));
    }
}

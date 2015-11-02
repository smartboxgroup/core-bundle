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
            [123456],
            [22.1767],
            [0],
        ];
    }

    public function invalidString() {
        return [
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
            ['0'],
            [1],
            ['1'],
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
     * @dataProvider validString
     * @param $string
     */
    public function testItShouldCheckAValidString($string) {
        $this->assertTrue($this->checker->canBeCastedToString($string));
    }

    /**
     * @dataProvider invalidString
     * @param $string
     */
    public function testItShouldCheckAnInvalidString($string) {
        $this->assertFalse($this->checker->canBeCastedToString($string));
    }

    /**
     * @dataProvider validBoolean
     * @param $bool
     */
    public function testItShouldCheckAValidBoolean($bool) {
        $this->assertTrue($this->checker->canBeCastedToBoolean($bool));
    }

    /**
     * @dataProvider invalidBoolean
     * @param $bool
     */
    public function testItShouldCheckAnInvalidBoolean($bool) {
        $this->assertFalse($this->checker->canBeCastedToBoolean($bool));
    }

    /**
     * @dataProvider validInteger
     * @param $int
     */
    public function testItShouldCheckAValidInteger($int) {
        $this->assertTrue($this->checker->canBeCastedToInteger($int));
    }

    /**
     * @dataProvider invalidInteger
     * @param $int
     */
    public function testItShouldCheckAnInvalidInteger($int) {
        $this->assertFalse($this->checker->canBeCastedToInteger($int));
    }

    /**
     * @dataProvider validDouble
     * @param $double
     */
    public function testItShouldCheckAValidDouble($double) {
        $this->assertTrue($this->checker->canBeCastedToDouble($double));
    }

    /**
     * @dataProvider invalidDouble
     * @param $double
     */
    public function testItShouldCheckAnInvalidDouble($double) {
        $this->assertFalse($this->checker->canBeCastedToDouble($double));
    }
}

<?php

namespace Smartbox\CoreBundle\Tests\Utils\Helper;

use Smartbox\CoreBundle\Utils\Helper\DateTimeCreator;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * @coversDefaultClass Smartbox\CoreBundle\Utils\Helper\DateTimeCreator
 */
class DateTimeCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getNowDateTime
     * @dataProvider microtimeProvider
     */
    public function testGetNowDateTime($microtime, $expectedDateTime)
    {

        $clockReflection = new \ReflectionClass(ClockMock::class);

        $reflectionProperty = $clockReflection->getProperty('now');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($microtime);

        ClockMock::register(DateTimeCreator::class);

        $datetime = DateTimeCreator::getNowDateTime();

        $this->assertEquals($expectedDateTime, $datetime);
    }

    public function microtimeProvider()
    {
        return [
            'with-decimal' => [
                1544538334.9465,
                new \DateTime('2018-12-11 14:25:34.946500'),
            ],
            'without-decimal' => [
                1544538334,
                new \DateTime('2018-12-11 14:25:34.000000'),
            ],
            'with-decimal-zero' => [
                1544538334.0,
                new \DateTime('2018-12-11 14:25:34.000000'),
            ],
        ];
    }
}

<?php

namespace Smartbox\CoreBundle\Tests\Utils\Helper;

use Smartbox\CoreBundle\Utils\Helper\DateTimeCreator;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * @coversDefaultClass \Smartbox\CoreBundle\Utils\Helper\DateTimeCreator
 */
class DateTimeCreatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers ::getNowDateTime
     * @dataProvider microtimeProvider
     */
    public function testGetNowDateTimePHP70($microtime, $expectedDateTime)
    {
        if (PHP_VERSION_ID >= 70100) {
            $this->markTestSkipped('Skipped as using a different method to create a datetime');
        }
        $clockReflection = new \ReflectionClass(ClockMock::class);

        $reflectionProperty = $clockReflection->getProperty('now');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($microtime);

        ClockMock::register(DateTimeCreator::class);

        $datetime = DateTimeCreator::getNowDateTime();

        $this->assertEquals($expectedDateTime, $datetime);
    }

    /**
     * @covers ::getNowDateTime
     */
    public function testGetNowDateTimePHP71()
    {
        if (PHP_VERSION_ID < 70100) {
            $this->markTestSkipped('Skipped as using a different method to create date time');
        }

        $datetime = DateTimeCreator::getNowDateTime();

        $this->assertInstanceOf(\DateTime::class, $datetime);
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

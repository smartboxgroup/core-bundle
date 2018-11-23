<?php

namespace Smartbox\CoreBundle\Tests\Utils\Monolog\Formatter;

use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;
use Smartbox\CoreBundle\Utils\Monolog\Formatter\JMSSerializerFormatter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class JMSSerializerFormatterTest.
 *
 * @coversDefaultClass \Smartbox\CoreBundle\Utils\Monolog\Formatter\JMSSerializerFormatter
 */
class JMSSerializerFormatterTest extends WebTestCase
{
    /** @var SerializerInterface */
    private $serializer;

    protected function setUp()
    {
        self::$class = null;
        static::bootKernel();
        $container = static::$kernel->getContainer();
        $this->serializer = $container->get('jms_serializer');
    }

    protected function tearDown()
    {
        parent::tearDown();
        self::$class = null;
        $this->serializer = null;
    }

    public function dataProviderForFormatter()
    {
        $data = [];
        for ($i = 0; $i < 3; ++$i) {
            $data[] = [
                \sprintf(
                    '[{"title":"title_%s","description":"description_%s","note":"note_%s","enabled":%s}]',
                    $i,
                    $i,
                    $i,
                    (($i % 2) ? 'true' : 'false')
                ),
                (new TestEntity())
                    ->setTitle('title_'.$i)
                    ->setDescription('description_'.$i)
                    ->setNote('note_'.$i)
                    ->setEnabled(($i % 2) ? true : false),
            ];
        }

        return $data;
    }

    /**
     * @dataProvider dataProviderForFormatter
     * @covers \Smartbox\CoreBundle\Utils\Monolog\Formatter\JMSSerializerFormatter::format
     *
     * @param $expected
     * @param $entity
     *
     * @return string
     */
    public function testFormat($expected, TestEntity $entity)
    {
        $formatter = new JMSSerializerFormatter();
        $formatter->setSerializer($this->serializer);

        $this->assertEquals($expected, $formatter->format([$entity]));
    }
}

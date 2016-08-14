<?php

namespace Smartbox\CoreBundle\Tests\Utils\Cache;

use Predis\ClientInterface;
use Predis\Response\ServerException;
use Smartbox\CoreBundle\Utils\Cache\PredisCacheService;

/**
 * Class PredisCacheServiceTest
 */
class PredisCacheServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var PredisCacheService */
    protected $service;

    /** @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $client;

    public function setUp()
    {
        $this->client = $this->getMockBuilder(ClientInterface::class)->getMock();
        $this->service = new PredisCacheService($this->client);
    }

    public function testItShouldSetAValueWithTTL()
    {
        $this->client->expects($this->once())
            ->method('__call')
            ->with('set', ['foo', serialize('bar'), 'EX', 666]);

        $this->service->set('foo', 'bar', 666);
    }

    public function testItShouldSetAValueWithoutTTL()
    {
        $this->client->expects($this->once())
            ->method('__call')
            ->with('set', ['foo', serialize('bar')]);

        $this->service->set('foo', 'bar', null);
    }

    /**
     * @dataProvider falsyValuesForKeyProvider
     *
     * @param mixed $key
     */
    public function testItShouldThrowAnExceptionWhenUsingFalsyKey($key)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->set($key, 'bar', null);
    }

    /**
     * @dataProvider invalidTTLProvider
     *
     * @param mixed $ttl
     */
    public function testItShouldThrowAnExceptionWhenUsingSetWithInvalidTTL($ttl)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->set('foo', 'bar', $ttl);
    }

    public function testItShouldGetAValue()
    {
        $this->client->expects($this->once())
            ->method('__call')
            ->with('get', ['foo'])
            ->willReturn(serialize('bar'));

        $this->assertEquals('bar', $this->service->get('foo'));
    }

    public function testItShouldCheckIfAKeyExists()
    {
        $this->client->expects($this->any())
            ->method('__call')
            ->will(
                $this->returnValueMap(
                    [
                        ['exists', ['foo'], true],
                        ['exists', ['bar'], false],
                    ]
                )
            );

        $this->assertTrue($this->service->exists('foo'));
        $this->assertFalse($this->service->exists('bar'));
    }

    public function testItShouldCheckIfAKeyExistsWithLimitTTL()
    {
        $this->client->expects($this->any())
            ->method('__call')
            ->will(
                $this->returnValueMap(
                    [
                        ['exists', ['foo'], true],
                        ['ttl', ['foo'], 10],
                    ]
                )
            );

        $this->assertFalse($this->service->exists('foo', 11));
    }

    /**
     * @dataProvider falsyValuesForKeyProvider
     *
     * @param mixed $key
     */
    public function testItShouldThrowAnExceptionWhenUsingGetWithFalsyKey($key)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->get($key);
    }

    /**
     * @dataProvider falsyValuesForKeyProvider
     *
     * @param mixed $key
     */
    public function testItShouldThrowAnExceptionWhenUsingExistsWithFalsyKey($key)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->exists($key);
    }

    /**
     * @dataProvider connectionIssuesCasesProvider
     */
    public function testItShouldLogExceptionInCaseOfConnectionIssues($method, $methodArgs, $expectsArgs, $assert)
    {
        // temporarily logs to a file
        $errorLogFile = __DIR__.sprintf('/../../Fixtures/stderr_%s.log', $method);
        $oldErrorLog = ini_get("error_log");
        ini_set("error_log", $errorLogFile);

        // mocking the specific call to throw an exception
        $this->client->expects($this->any())
            ->method('__call')
            ->with($method, $expectsArgs)
            ->willThrowException(new ServerException(sprintf('when calling "%s"', $method)));

        // checking the return type from the method call
        $this->assertEquals($assert, call_user_func_array([$this->service, $method], $methodArgs));

        // restores error logging
        ini_set("error_log", $oldErrorLog);

        // checks logged message and deletes temp file
        $loggedMessage = file_get_contents($errorLogFile);
        $this->assertContains(sprintf('Error: Redis service is down: when calling "%s"', $method), $loggedMessage);
        unlink($errorLogFile);
    }

    public function invalidTTLProvider()
    {
        return [
            ['some string'],
            [[1, 2, 3]], // an array
            [new \stdClass()], // an object
            [-666], // a negative value
            [66.6], // a decimal value
        ];
    }

    public function falsyValuesForKeyProvider()
    {
        return [
            [''],
            [false],
            [0],
            [null],
            [[]],
        ];
    }

    public function connectionIssuesCasesProvider()
    {
        return [
            [
                'method' => 'set',
                'methodArgs' => ['foo', 'bar', null],
                'expectsArgs' => ['foo', serialize('bar')],
                'assert' => false,
            ],
            [
                'method' => 'get',
                'methodArgs' => ['foo'],
                'expectsArgs' => ['foo'],
                'assert' => null,
            ],
            [
                'method' => 'exists',
                'methodArgs' => ['foo'],
                'expectsArgs' => ['foo'],
                'assert' => false,
            ],
        ];
    }
}

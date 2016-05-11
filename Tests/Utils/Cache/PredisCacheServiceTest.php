<?php

namespace Utils\Cache;

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

    public function setup()
    {
        $this->client = $this->getMockBuilder(ClientInterface::class)->getMock();
        $this->service = new PredisCacheService($this->client);
    }

    public function testItShouldSetAValueWithTTL()
    {
        $this->client->expects($this->once())
            ->method('__call')
            ->with('set', ['foo', serialize('bar'), 'EX', 666])
        ;

        $this->service->set('foo', 'bar', 666);
    }

    public function testItShouldSetAValueWithoutTTL()
    {
        $this->client->expects($this->once())
            ->method('__call')
            ->with('set', ['foo', serialize('bar')])
        ;

        $this->service->set('foo', 'bar', null);
    }

    /**
     * @param mixed $key
     * @dataProvider falsyValuesForKey
     */
    public function testItShouldThrowAnExceptionWhenUsingFalsyKey($key)
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->service->set($key, 'bar', null);
    }

    /**
     * @dataProvider invalidTTLProvider
     * @param mixed $ttl
     */
    public function testItShouldThrowAnExceptionWhenUsingSetWithInvalidTTL($ttl)
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->service->set('foo', 'bar', $ttl);
    }

    public function testItShouldGetAValue()
    {
        $this->client->expects($this->once())
            ->method('__call')
            ->with('get', ['foo'])
            ->willReturn(serialize('bar'))
        ;

        $this->assertEquals('bar', $this->service->get('foo'));
    }

    public function testItShouldCheckIfAKeyExists()
    {
        $this->client->expects($this->any())
            ->method('__call')
            ->will($this->returnValueMap([
                ['exists', ['foo'], true],
                ['exists', ['bar'], false],
            ]))
        ;

        $this->assertTrue($this->service->exists('foo'));
        $this->assertFalse($this->service->exists('bar'));
    }

    public function testItShouldCheckIfAKeyExistsWithLimitTTL()
    {
        $this->client->expects($this->any())
            ->method('__call')
            ->will($this->returnValueMap([
                ['exists', ['foo'], true],
                ['ttl', ['foo'], 10],
            ]))
        ;

        $this->assertFalse($this->service->exists('foo', 11));
    }

    /**
     * @param mixed $key
     * @dataProvider falsyValuesForKey
     */
    public function testItShouldThrowAnExceptionWhenUsingGetWithFalsyKey($key)
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->service->get($key);
    }

    /**
     * @param mixed $key
     * @dataProvider falsyValuesForKey
     */
    public function testItShouldThrowAnExceptionWhenUsingExistsWithFalsyKey($key)
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->service->exists($key);
    }

    public function testItShouldLogExceptionInCaseOfConnectionIssues()
    {
        // temporarily logs to a file
        $errorLogFile = __DIR__.'/../../Fixtures/stderr.log';
        $oldErrorLog = ini_get("error_log");
        ini_set("error_log", $errorLogFile);

        // SET
        $this->client->expects($this->any())
            ->method('__call')
            ->with('set', ['foo', serialize('bar')])
            ->willThrowException(new ServerException('when calling "set"'))
        ;
        $this->assertFalse($this->service->set('foo', 'bar', null));

        // GET
        $this->client->expects($this->any())
            ->method('__call')
            ->with('get', ['foo'])
            ->willThrowException(new ServerException('when calling "get"'))
        ;
        $this->assertNull($this->service->get('foo'));

        // EXISTS
        $this->client->expects($this->any())
            ->method('__call')
            ->with('exists', ['foo'])
            ->willThrowException(new ServerException('when calling "exists"'))
        ;
        $this->assertFalse($this->service->exists('foo'));

        // restores error logging
        ini_set("error_log", $oldErrorLog);

        // checks logged message and deletes temp file
        $loggedMessage = file_get_contents($errorLogFile);
        $this->assertContains('Error: Redis service is down: when calling "set"', $loggedMessage);
        $this->assertContains('Error: Redis service is down: when calling "get"', $loggedMessage);
        $this->assertContains('Error: Redis service is down: when calling "exists"', $loggedMessage);
        unlink($errorLogFile);
    }

    public function invalidTTLProvider()
    {
        return [
            ['some string'],
            [[1,2,3]], // an array
            [new \stdClass()], // an object
            [-666], // a negative value
            [66.6], // a decimal value
        ];
    }

    public function falsyValuesForKey()
    {
        return [
            [''],
            [false],
            [0],
            [null],
            [[]]
        ];
    }
}

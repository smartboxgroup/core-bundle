<?php

declare(strict_types=1);

namespace Smartbox\CoreBundle\Tests\Command;

use Smartbox\CoreBundle\Utils\SmokeTest\Output\SmokeTestOutputInterface;
use Smartbox\CoreBundle\Utils\SmokeTest\SmokeTestInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Smartbox\CoreBundle\Command\SmokeTestRunCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group smoke-test
 */
class SmokeTestRunCommandTest extends KernelTestCase
{
    public function testExecuteCommand()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new SmokeTestRunCommand());

        $command = $application->find('smartbox:smoke-test');

        $input = ['command' => $command->getName()];

        $commandTester = new CommandTester($command);
        $commandTester->execute($input);

        $output = $commandTester->getDisplay();

        $this->assertInternalType('string', $output);
        $this->assertContains('Smoke Tests', $output);
        $this->assertNotContains('Error', $output);
    }

    public function testSmokeTestCommandNoLabels()
    {
        $smokeTestOutput = $this->createMock(SmokeTestOutputInterface::class);
        $smokeTestOutput->expects($this->any())->method('isOK')->will($this->returnValue(true));
        $smokeTestOutput->expects($this->any())->method('getMessages')->will($this->returnValue([]));

        $smokeTest1 = $this->createMock(SmokeTestInterface::class);
        $smokeTest1->expects($this->never())->method('run')->will($this->returnValue($smokeTestOutput));
        $smokeTest1->expects($this->never())->method('getDescription');

        $smokeTest2 = $this->createMock(SmokeTestInterface::class);
        $smokeTest2->expects($this->once())->method('run')->will($this->returnValue($smokeTestOutput));
        $smokeTest2->expects($this->once())->method('getDescription');

        $smokeTestRunCommand = new SmokeTestRunCommand();
        $smokeTestRunCommand->addTest('id1', $smokeTest1, 'run', 'getDescription', ['wip']);
        $smokeTestRunCommand->addTest('id2', $smokeTest2, 'run', 'getDescription', ['critical']);
        
        $smokeTestRunCommand->run(new ArrayInput(['--label' => []]), new NullOutput());
    }

    public function testSmokeTestCommandWipLabel()
    {
        $smokeTestOutput = $this->createMock(SmokeTestOutputInterface::class);
        $smokeTestOutput->expects($this->any())->method('isOK')->will($this->returnValue(true));
        $smokeTestOutput->expects($this->any())->method('getMessages')->will($this->returnValue([]));

        $smokeTest1 = $this->createMock(SmokeTestInterface::class);
        $smokeTest1->expects($this->once())->method('run')->will($this->returnValue($smokeTestOutput));
        $smokeTest1->expects($this->once())->method('getDescription');

        $smokeTest2 = $this->createMock(SmokeTestInterface::class);
        $smokeTest2->expects($this->never())->method('run')->will($this->returnValue($smokeTestOutput));
        $smokeTest2->expects($this->never())->method('getDescription');

        $smokeTestRunCommand = new SmokeTestRunCommand();
        $smokeTestRunCommand->addTest('id1', $smokeTest1, 'run', 'getDescription', ['wip']);
        $smokeTestRunCommand->addTest('id2', $smokeTest2, 'run', 'getDescription', ['critical']);

        $smokeTestRunCommand->run(new ArrayInput(['--label' => ['wip']]), new NullOutput());
    }

    public function testSmokeTestCommandAllTests()
    {
        $smokeTestOutput = $this->createMock(SmokeTestOutputInterface::class);
        $smokeTestOutput->expects($this->any())->method('isOK')->will($this->returnValue(true));
        $smokeTestOutput->expects($this->any())->method('getMessages')->will($this->returnValue([]));

        $smokeTest1 = $this->createMock(SmokeTestInterface::class);
        $smokeTest1->expects($this->once())->method('run')->will($this->returnValue($smokeTestOutput));
        $smokeTest1->expects($this->once())->method('getDescription');

        $smokeTest2 = $this->createMock(SmokeTestInterface::class);
        $smokeTest2->expects($this->once())->method('run')->will($this->returnValue($smokeTestOutput));
        $smokeTest2->expects($this->once())->method('getDescription');

        $smokeTestRunCommand = new SmokeTestRunCommand();
        $smokeTestRunCommand->addTest('id1', $smokeTest1, 'run', 'getDescription', ['wip']);
        $smokeTestRunCommand->addTest('id2', $smokeTest2, 'run', 'getDescription', ['critical']);

        $smokeTestRunCommand->run(new ArrayInput(['--all' => true]), new NullOutput());
    }
}

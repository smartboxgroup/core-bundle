<?php

declare(strict_types=1);

namespace Smartbox\CoreBundle\Tests\Command;

use Smartbox\CoreBundle\Utils\SmokeTest\Output\SmokeTestOutputInterface;
use Smartbox\CoreBundle\Utils\SmokeTest\SmokeTestInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Smartbox\CoreBundle\Command\SmokeTestRunCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group smoke-test
 */
class SmokeTestRunCommandTest extends KernelTestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var array
     */
    private $commandConfiguration = [];

    /**
     * @var Command
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->application = new Application($kernel);
        $this->application->add(new SmokeTestRunCommand());

        $this->command = $this->application->find('smartbox:smoke-test');
        $this->commandTester = new CommandTester($this->command);
    }

    public function tearDown()
    {
        $this->application = null;
        $this->command = null;
        $this->commandTester = null;
        $this->commandConfiguration = null;
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

    public function testExecute()
    {
        $output = $this->execute();

        $this->assertInternalType('string', $output);
        $this->assertContains('Smoke Tests', $output);
        $this->assertNotContains('Error', $output);
    }

    public function testExecuteWithLabels()
    {
        $label = ['-l' => 'important'];

        $this->setCommandConfiguration($label);
        $output = $this->execute();

        $this->assertInternalType('string', $output);
        $this->assertContains('Smoke Tests', $output);
        $this->assertNotContains('Error', $output);
    }

    private function execute()
    {
        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ],
            $this->commandConfiguration
        );

        return $this->commandTester->getDisplay();
    }

    private function setCommandConfiguration($options = [])
    {
        $this->commandConfiguration = $options;
    }
}

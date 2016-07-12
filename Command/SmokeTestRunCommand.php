<?php

namespace Smartbox\CoreBundle\Command;

use Smartbox\CoreBundle\Utils\SmokeTest\Output\SmokeTestOutputInterface;
use Smartbox\CoreBundle\Utils\SmokeTest\SmokeTestInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SmokeTestRunCommand extends ContainerAwareCommand
{
    /** @var SmokeTestInterface[] */
    protected $smokeTests = [];

    /** @var  InputInterface */
    protected $in;

    /** @var  OutputInterface */
    protected $out;

    protected function configure()
    {
        $this
            ->setName('smartbox:smoke-test')
            ->setDescription('Run all services tagged with "smartcore.smoke_test"')
            ->addOption('silent', null, InputOption::VALUE_NONE, 'If in silent mode this command will return only exit code (0 or 1)')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Show output in JSON format.')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'File path to write')
        ;
    }

    public function addTest($id, SmokeTestInterface $smokeTest, $runMethod = 'run', $descriptionMethod = 'getDescription')
    {
        $key = $id . '_' . $runMethod;
        if (array_key_exists($key, $this->smokeTests)) {
            throw new \RuntimeException(sprintf('Test with id "%s" is already added.', $id));
        }

        $this->smokeTests[$key] = [
            'service' => $smokeTest,
            'id' => $id,
            'runMethod' => $runMethod,
            'descriptionMethod' => $descriptionMethod,
            ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $this->in = $in;
        $this->out = $out;

        $silent = $in->getOption('silent');
        $json = $in->getOption('json');
        $output = $in->getOption('output');
        $exitCode = 0;

        if (!$silent && !$json) {
            $this->out->writeln('');
            $this->out->writeln('<info>###################################</info>');
            $this->out->writeln('<info>##          Smoke Tests          ##</info>');
            $this->out->writeln('<info>###################################</info>');
        }

        $content = array();
        foreach ($this->smokeTests as $key => $smokeTestInfo) {
            $smokeTest = $smokeTestInfo['service'];
            $id = $smokeTestInfo['id'];
            $runMethod = $smokeTestInfo['runMethod'];
            $descriptionMethod = $smokeTestInfo['descriptionMethod'];

            $smokeTestOutput = null;
            if (!$silent && !$json) {
                $this->out->writeln("\n");
                $this->out->writeln('Running @SmokeTest with ID: ' . '<info>' . $id . '</info> and method: <info>' . $runMethod . '</info>');
                $this->out->writeln('Description: ' . '<info>' . $smokeTest->$descriptionMethod() . '</info>');

                try {
                    /** @var SmokeTestOutputInterface $smokeTestOutput */
                    $smokeTestOutput = $smokeTest->$runMethod();

                    if (!$smokeTestOutput instanceof SmokeTestOutputInterface) {
                        throw new \RuntimeException("A smoke test method must return an object implementing SmokeTestOutputInterface. Wrong return type for smoke test: $id with method $runMethod");
                    }

                    $this->out->writeln('STATUS: ' . ($smokeTestOutput->isOK() ? '<info>Success</info>' : '<error>Failure</error>'));
                    $this->out->writeln('MESSAGE:');
                    foreach ($smokeTestOutput->getMessages() as $message) {
                        $this->out->writeln("\t" . ' - ' . $message);
                    }

                    if (!$smokeTestOutput->isOK()) {
                        $exitCode = 1;
                    }
                } catch (\Exception $e) {
                    $this->out->writeln('STATUS: <error>Failure</error>');
                    $this->out->writeln('MESSAGE:');
                    $this->out->writeln("\t" . sprintf(' - [%s] %s', get_class($e), $e->getMessage()));

                    $exitCode = 1;
                }
                $this->out->writeln("\n---------------------------------");
            } else {
                $result = [
                    'id' => $id,
                    'method' => $runMethod,
                    'description' => $smokeTest->$descriptionMethod(),
                ];

                try {
                    /** @var SmokeTestOutputInterface $smokeTestOutput */
                    $smokeTestOutput = $smokeTest->$runMethod();

                    if (!$smokeTestOutput instanceof SmokeTestOutputInterface) {
                        throw new \RuntimeException("A smoke test method must return an object implementing SmokeTestOutputInterface. Wrong return type for smoke test: $id with method $runMethod");
                    }
                    $result['result'] = implode("\n", $smokeTestOutput->getMessages());
                    $result['failed'] = !$smokeTestOutput->isOK();

                    if (!$smokeTestOutput->isOK()) {
                        $exitCode = 1;
                    }
                } catch(\Exception $e) {
                    $result['result'] = sprintf('[%s] %s', get_class($e), $e->getMessage());
                    $result['failed'] = true;

                    $exitCode = 1;
                }
                $content[] = $result;
            }
        }

        if ($json) {
            $content = json_encode($content);

            if ($output) {
                file_put_contents($output, $content);
            } elseif (!$silent) {
                $this->out->writeln($content);
            }
        }

        return $exitCode;
    }
}

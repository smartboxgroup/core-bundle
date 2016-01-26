<?php

namespace Smartbox\CoreBundle\Command;

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
            ->setDescription('Run all services tagged with "smartbox.smoke_test"')
            ->addOption('silent', null, InputOption::VALUE_NONE, 'If in silent mode this command will return only exit code (0 or 1)')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Show output in JSON format.')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'File path to write')
        ;
    }

    public function addTest($id, SmokeTestInterface $smokeTest)
    {
        if (array_key_exists($id, $this->smokeTests)) {
            throw new \RuntimeException(sprintf('Test with id "%s" is already added.', $id));
        }

        $this->smokeTests[$id] = $smokeTest;
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
            $this->out->writeln("");
            $this->out->writeln("<info>###################################</info>");
            $this->out->writeln("<info>##          Smoke Tests          ##</info>");
            $this->out->writeln("<info>###################################</info>");
        }

        $content = array();
        foreach ($this->smokeTests as $id => $smokeTest) {
            $smokeTestOutput = null;
            if (!$silent && !$json) {
                $this->out->writeln("\n");
                $this->out->writeln('Running @SmokeTest with ID: ' . "<info>" . $id . "</info>");
                $this->out->writeln('Description: ' . "<info>" . $smokeTest->getDescription() . "</info>");

                $smokeTestOutput = $smokeTest->run();

                $this->out->writeln('STATUS: ' . ($smokeTestOutput->isOK()? '<info>Success</info>' : '<error>Failure</error>'));
                $this->out->writeln('MESSAGE:');
                foreach ($smokeTestOutput->getMessages() as $message) {
                    $this->out->writeln("\t" . ' - ' . $message);
                }
                $this->out->writeln("\n---------------------------------");
            } else {
                $smokeTestOutput = $smokeTest->run();
                $result = array(
                    'id' => $id,
                    'description' => $smokeTest->getDescription(),
                    'result' => implode("\n", $smokeTestOutput->getMessages()),
                    'failed' => !$smokeTestOutput->isOK(),
                );

                $content[] = $result;
            }

            if (!$smokeTestOutput->isOK()) {
                $exitCode = 1;
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
<?php

declare(strict_types=1);

namespace Smartbox\CoreBundle\Command;

use Smartbox\CoreBundle\Utils\SmokeTest\Input\SmokeTestLabel;
use Smartbox\CoreBundle\Utils\SmokeTest\Output\SmokeTestOutputInterface;
use Smartbox\CoreBundle\Utils\SmokeTest\Output\SmokeTestOutputMessage;
use Smartbox\CoreBundle\Utils\SmokeTest\SmokeTestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SmokeTestRunCommand extends KernelTestCase
{
    /** @var SmokeTestInterface[] */
    protected $smokeTests = [];

    /** @var InputInterface */
    protected $in;

    /** @var OutputInterface */
    protected $out;

    protected function configure()
    {
        $this
            ->setName('smartbox:smoke-test')
            ->setDescription('Run all services tagged with "smartcore.smoke_test"')
            ->addOption(
                'label',
                'l',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'A set of label that will be used to filter the tests',
                []
            )
            ->addOption(
                'skip',
                'x',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'A set of tests id that will be skipped',
                []
            )->addOption(
                'skipLabel',
                'z',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'A set of tests label that will be skipped. By default all the tests with label wip will be skipped',
                [SmokeTestLabel::LABEL_WIP]
            )->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'If set will runs all tests, including tests skipped by default'
            )
            ->addOption('showSkipped', null, InputOption::VALUE_NONE, 'If set will show the list of skipped tests')
            ->addOption(
                'silent',
                null,
                InputOption::VALUE_NONE,
                'If in silent mode this command will return only exit code (0 or 1)'
            )
            ->addOption('json', null, InputOption::VALUE_NONE, 'Show output in JSON format.')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'File path to write')
            ->addOption('list', 'L', InputOption::VALUE_NONE, 'List all the possible tests')
            ->addArgument('test', InputArgument::OPTIONAL, 'Specify the id of a single test to execute');
    }

    public function addTest(
        $id,
        SmokeTestInterface $smokeTest,
        $runMethod = 'run',
        $descriptionMethod = 'getDescription',
        $labels = []
    ) {
        $key = $id.'_'.$runMethod;
        if (\array_key_exists($key, $this->smokeTests)) {
            throw new \RuntimeException(\sprintf('Test with id "%s" is already added.', $id));
        }

        $this->smokeTests[$key] = [
            'service' => $smokeTest,
            'id' => $id,
            'runMethod' => $runMethod,
            'descriptionMethod' => $descriptionMethod,
            'labels' => $labels,
        ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $this->in = $in;
        $this->out = $out;

        $labels = $in->getOption('label');
        $skipTests = $in->getOption('skip');
        $showSkipped = $in->getOption('showSkipped');
        $silent = $in->getOption('silent');
        $json = $in->getOption('json');
        $output = $in->getOption('output');
        $test = $in->getArgument('test');
        $skipLabels = $in->getOption('skipLabel');
        $allTests = $in->getOption('all');
        $exitCode = 0;

        if (!$silent && !$json) {
            $this->out->writeln('');
            $this->out->writeln('<info>###################################</info>');
            $this->out->writeln('<info>##          Smoke Tests          ##</info>');
            $this->out->writeln('<info>###################################</info>');
        }

        $skipped = [];
        $smokeTests = $this->smokeTests;

        if ($in->getOption('list')) {
            $testNames = $this->getTestNames();
            foreach ($testNames as $name) {
                $this->out->writeln($name);
            }

            return 0;
        }

        if ($test) {
            // executes a single test
            $showSkipped = false;
            $testNames = $this->getTestNames();
            $smokeTests = \array_filter(
                $smokeTests,
                function ($key) use ($test) {
                    return $key === $test;
                },
                ARRAY_FILTER_USE_KEY
            );
            if (empty($smokeTests)) {
                throw new \RuntimeException(
                    \sprintf('Test "%s" not found. Available tests: %s', $test, \json_encode($testNames))
                );
            }
        } else {
            // executes all tests (checking for label filters)
            \ksort($smokeTests, SORT_STRING);

            // filter by labels and skipLabels options.
            // If no label was passed as parameter it will filter by skip (wip) labels
            if (!$allTests) {
                $smokeTests = $this->filterByLabels($smokeTests, $labels, $skipLabels, $skipped);
            }

            // filter by skiptTests (id) options
            if (!empty($skipTests)) {
                $smokeTests = $this->filterBySkipTests($smokeTests, $skipTests, $skipped);
            }
        }

        $content = [];
        foreach ($smokeTests as $key => $smokeTestInfo) {
            $smokeTest = $smokeTestInfo['service'];
            $id = $smokeTestInfo['id'];
            $runMethod = $smokeTestInfo['runMethod'];
            $descriptionMethod = $smokeTestInfo['descriptionMethod'];

            $smokeTestOutput = null;
            if (!$silent && !$json) {
                $this->applyOutputStyles();

                $this->out->writeln("\n");
                $this->out->writeln(
                    'Running @SmokeTest with ID: '.'<info>'.$id.'</info> and method: <info>'.$runMethod.'</info>'
                );
                $this->out->writeln('Description: '.'<info>'.$smokeTest->$descriptionMethod().'</info>');

                try {
                    /** @var SmokeTestOutputInterface $smokeTestOutput */
                    $smokeTestOutput = $smokeTest->$runMethod();

                    if (!$smokeTestOutput instanceof SmokeTestOutputInterface) {
                        throw new \RuntimeException(
                            "A smoke test method must return an object implementing SmokeTestOutputInterface. Wrong return type for smoke test: $id with method $runMethod"
                        );
                    }

                    $this->out->writeln(
                        'STATUS: '.($smokeTestOutput->isOK(
                        ) ? '<success>Success</success>' : '<failure>Failure</failure>')
                    );
                    $this->out->writeln('LABELS: '.\json_encode($smokeTestInfo['labels']));
                    $this->out->writeln('MESSAGE:');
                    foreach ($smokeTestOutput->getMessages() as $message) {
                        $this->out->writeln(
                            "\t".
                            \sprintf(
                                ' - <%1$s>%2$s</%1$s>',
                                $message->getType(),
                                $message->getValue()
                            )
                        );
                    }

                    if (!$smokeTestOutput->isOK()) {
                        $exitCode = 1;
                    }
                } catch (\Exception $e) {
                    $this->out->writeln('STATUS: <failure>Failure</failure>');
                    $this->out->writeln('MESSAGE:');
                    $this->out->writeln(
                        "\t".
                        \sprintf(
                            ' - <%1$s>[%2$s] %3$s</%1$s>',
                            SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_FAILURE,
                            \get_class($e),
                            $e->getMessage()
                        )
                    );

                    $exitCode = 1;
                }
                $this->out->writeln("\n---------------------------------");
            } else {
                $result = [
                    'id' => $id,
                    'method' => $runMethod,
                    'description' => $smokeTest->$descriptionMethod(),
                    'labels' => $smokeTestInfo['labels'],
                ];

                try {
                    /** @var SmokeTestOutputInterface $smokeTestOutput */
                    $smokeTestOutput = $smokeTest->$runMethod();

                    if (!$smokeTestOutput instanceof SmokeTestOutputInterface) {
                        throw new \RuntimeException(
                            "A smoke test method must return an object implementing SmokeTestOutputInterface. Wrong return type for smoke test: $id with method $runMethod"
                        );
                    }
                    $messages = $smokeTestOutput->getMessages();
                    $result['result'] = [];
                    foreach ($messages as $message) {
                        $result['result'][] = [
                            'type' => $message->getType(),
                            'value' => $message->getValue(),
                        ];
                    }

                    $result['failed'] = !$smokeTestOutput->isOK();

                    if (!$smokeTestOutput->isOK()) {
                        $exitCode = 1;
                    }
                } catch (\Exception $e) {
                    $result['result'][] = [
                        'type' => SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_FAILURE,
                        'value' => \sprintf('[%s] %s', \get_class($e), $e->getMessage()),
                    ];
                    $result['failed'] = true;

                    $exitCode = 1;
                }
                $content[] = $result;
            }
        }

        if ($showSkipped && !empty($skipped)) {
            foreach ($skipped as $name => $smokeTestInfo) {
                $descriptionMethod = $smokeTestInfo['descriptionMethod'];
                if (!$silent && !$json) {
                    $this->out->writeln("\n");
                    $this->out->writeln(
                        '@SmokeTest with ID: '.'<info>'.$smokeTestInfo['id'].'</info> and method: <info>'.$smokeTestInfo['runMethod'].'</info>'
                    );
                    $this->out->writeln(
                        'Description: '.'<info>'.$smokeTestInfo['service']->$descriptionMethod().'</info>'
                    );
                    $this->out->writeln('STATUS: <skipped>Skipped</skipped>');
                    $this->out->writeln('LABELS: '.\json_encode($smokeTestInfo['labels']));
                    $this->out->writeln("\n---------------------------------");
                } else {
                    $content[] = [
                        'id' => $smokeTestInfo['id'],
                        'method' => $smokeTestInfo['runMethod'],
                        'description' => $smokeTestInfo['service']->$descriptionMethod(),
                        'labels' => $smokeTestInfo['labels'],
                        'failed' => false,
                        'result' => [],
                        'skipped' => true,
                    ];
                }
            }
        }

        if ($json) {
            $content = \json_encode($content);

            if ($output) {
                \file_put_contents($output, $content);
            } elseif (!$silent) {
                $this->out->writeln($content);
            }
        }

        return $exitCode;
    }

    protected function applyOutputStyles()
    {
        $this->out->getFormatter()->setStyle(
            SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_INFO,
            new OutputFormatterStyle('white')
        );
        $this->out->getFormatter()->setStyle(
            SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_SUCCESS,
            new OutputFormatterStyle('green')
        );
        $this->out->getFormatter()->setStyle(
            SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_FAILURE,
            new OutputFormatterStyle('red')
        );
        $this->out->getFormatter()->setStyle(
            SmokeTestOutputMessage::OUTPUT_MESSAGE_TYPE_SKIPPED,
            new OutputFormatterStyle('yellow')
        );
    }

    /**
     * Returns and array of all the test names that this command can run.
     *
     * @return array
     */
    public function getTestNames()
    {
        return \array_keys($this->smokeTests);
    }

    /**
     * Filter smoke tests according to the label.
     *
     * @param array $smokeTests
     * @param array $labels
     * @param array $skipLabels
     * @param array $skipped
     *
     * @return array
     */
    protected function filterByLabels(array $smokeTests, array $labels, array $skipLabels, array &$skipped): array
    {
        return \array_filter($smokeTests, function ($smokeTestInfo) use ($labels, &$skipped, $skipLabels) {
            $key = $smokeTestInfo['id'];
            if (!empty(\array_intersect($labels, $smokeTestInfo['labels'])) ||
                (!empty(\array_diff($smokeTestInfo['labels'], $skipLabels)) && empty($labels))
            ) {
                return $smokeTestInfo;
            }

            $skipped[$key] = $smokeTestInfo;
        });
    }

    /**
     * Filter smoke tests according to the skipTests option.
     *
     * @param array $smokeTests
     * @param array $skipTests
     * @param array $skipped
     *
     * @return array
     */
    protected function filterBySkipTests(array $smokeTests, array $skipTests, array &$skipped): array
    {
        return \array_filter($smokeTests, function ($smokeTestInfo) use ($skipTests, &$skipped) {
            $key = $smokeTestInfo['id'];
            if (\in_array($key, $skipTests)) {
                $skipped[$key] = $smokeTestInfo;
            }

            return $smokeTestInfo;
        });
    }
}

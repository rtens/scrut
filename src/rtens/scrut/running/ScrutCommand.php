<?php
namespace rtens\scrut\running;

use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\listeners\FailConsoleListener;
use rtens\scrut\listeners\MemoryConsoleListener;
use rtens\scrut\listeners\TimeConsoleListener;
use rtens\scrut\listeners\VerboseConsoleListener;
use rtens\scrut\TestName;
use watoki\factory\Factory;

class ScrutCommand {

    private static $configFileNames = [
        'scrut.json',
        'scrut.json.dist'
    ];

    private static $defaultConfiguration = [
        'runner' => TestRunner::class,
        'listeners' => [
            'Compact' => CompactConsoleListener::class,
            'Fail' => FailConsoleListener::class,
            'Memory' => MemoryConsoleListener::class,
            'Time' => TimeConsoleListener::class,
            'Verbose' => VerboseConsoleListener::class
        ]
    ];

    /** @var Factory */
    private $factory;

    function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    /**
     * @param string $cwd
     * @param string[] $arguments The command arguments (without the name of the script itself)
     * @return int The exit value
     * @throws \Exception
     */
    public function execute($cwd, $arguments) {
        $runConfig = [];
        $name = null;

        foreach ($arguments as $a) {
            if (substr($a, 0, 2) == '-l') {
                $runConfig['listen'][] = substr($a, 2);
            } else {
                $name = TestName::parse($a);
            }
        }

        $configuration = $this->factory->getInstance(TestRunConfiguration::class, [
            $this->factory,
            $cwd,
            array_replace_recursive(
                self::$defaultConfiguration,
                $this->readConfiguration(),
                $runConfig
            )
        ]);

        return $configuration->getRunner()->run($name) ? 0 : 1;
    }


    /**
     * @return array
     * @throws \Exception
     */
    private function readConfiguration() {
        foreach (self::$configFileNames as $file) {
            if (file_exists($file)) {
                $config = json_decode(file_get_contents($file), true);
                if ($config !== null) {
                    return $config;
                }
                throw new \Exception("[$file] contains invalid JSON");
            }
        }

        return [];
    }
}
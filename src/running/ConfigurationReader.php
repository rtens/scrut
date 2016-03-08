<?php
namespace rtens\scrut\running;

use watoki\factory\Factory;
use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\listeners\FailConsoleListener;
use rtens\scrut\listeners\MemoryConsoleListener;
use rtens\scrut\listeners\TimeConsoleListener;
use rtens\scrut\listeners\VerboseConsoleListener;

class ConfigurationReader {

    private static $defaultConfigFiles = [
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

    /** @var string */
    private $cwd;

    function __construct($cwd, Factory $factory) {
        $this->cwd = $cwd;
        $this->factory = $factory;
    }

    /**
     * @param null|string $file
     * @param array $mergeWith
     * @return TestRunConfiguration
     */
    public function read($file = null, array $mergeWith = []) {
        $config = array_replace_recursive(
            self::$defaultConfiguration,
            $this->readFromFile($file),
            $mergeWith
        );

        return $this->factory->getInstance(TestRunConfiguration::class, [$this->factory, $this->cwd, $config]);
    }

    private function readFromFile($configFile = null) {
        $files = $configFile ? [$configFile] : self::$defaultConfigFiles;

        foreach ($files as $file) {
            $file = $this->cwd . DIRECTORY_SEPARATOR . $file;

            if (file_exists($file)) {
                $config = json_decode(file_get_contents($file), true);
                if ($config !== null) {
                    return $config;
                }
                throw new \Exception("[$file] contains invalid JSON");
            }
        }

        if ($configFile) {
            throw new \Exception("Invalid configuration");
        }

        return [];
    }
}
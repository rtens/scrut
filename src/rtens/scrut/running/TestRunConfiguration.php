<?php
namespace rtens\scrut\running;

use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\TestFilter;
use watoki\factory\Factory;

class TestRunConfiguration {

    private $workingDirectory;

    private $config;

    private $factory;

    private static $defaultListeners = [
        CompactConsoleListener::class
    ];

    function __construct(Factory $factory, $workingDirectory, array $config) {
        $factory->setSingleton(get_class($this), $this);

        $this->workingDirectory = $workingDirectory;
        $this->config = $config;
        $this->factory = $factory;
    }

    /**
     * @return \rtens\scrut\running\TestRunner
     */
    public function getRunner() {
        return $this->factory->getInstance($this->get('runner', TestRunner::class));
    }

    /**
     * @throws \InvalidArgumentException
     * @return \rtens\scrut\TestRunListener[]
     */
    public function getListeners() {
        $listeners = [];
        foreach ($this->get('listen', self::$defaultListeners) as $class) {
            if ($this->get('listeners/' . $class)) {
                $class = $this->get('listeners/' . $class);
            }

            try {
                $listeners[] = $this->factory->getInstance($class);
            } catch (\ReflectionException $e) {
                throw new \InvalidArgumentException("Could not find listener [$class]", 0, $e);
            }
        }
        return $listeners;
    }

    /**
     * @return TestFilter
     */
    public function getFilter() {
        $filter = new TestFilter();

        if ($this->get('filter')) {
            $filter->filterClass(function (\ReflectionClass $class) {
                return preg_match($this->get('filter'), $class->getShortName());
            });
        }

        return $filter;
    }

    /**
     * @return \rtens\scrut\Test
     */
    public function getTest() {
        if ($this->get('suite/file')) {
            return new FileTestSuite($this->getFilter(), $this->fullPath(), $this->get('suite/file'));
        }
        return new GenericTestSuite('Test');
    }

    public function fullPath($path = '') {
        return $this->workingDirectory . ($path ? (DIRECTORY_SEPARATOR . $path) : '');
    }

    private function get($path, $default = null) {
        $config = $this->config;

        foreach (explode('/', $path) as $key){
            if (array_key_exists($key, $config)) {
                $config = $config[$key];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
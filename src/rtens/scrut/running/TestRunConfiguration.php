<?php
namespace rtens\scrut\running;

use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\TestName;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
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
        return $this->buildTestSuite($this->get('suite', ['name' => 'Test']));
    }

    private function buildTestSuite($suiteConfig, TestName $parent = null) {
        if (array_key_exists('file', $suiteConfig)) {
            return new FileTestSuite($this->getFilter(), $this->fullPath(), $this->get('suite/file'), $parent);
        } else if (array_key_exists('class', $suiteConfig)) {
            $class = $suiteConfig['class'];
            if (is_subclass_of($class, StaticTestSuite::class)) {
                return new $class($this->getFilter(), $parent);
            }
            return new PlainTestSuite($this->getFilter(), $class, $parent);
        } else if (array_key_exists('name', $suiteConfig)) {
            $suite = new GenericTestSuite($suiteConfig['name'], $parent);
            if (array_key_exists('suites', $suiteConfig)) {
                foreach ($suiteConfig['suites'] as $child) {
                    $suite->add($this->buildTestSuite($child, $suite->getName()));
                }
            }

            return $suite;
        }

        throw new \Exception('Invalid suite configuration');
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
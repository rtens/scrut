<?php
namespace rtens\scrut\running;

use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\TestName;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\TestFilter;
use rtens\scrut\tests\TestSuiteFactory;
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

    public function getTestSuiteFactory() {
        return new TestSuiteFactory();
    }

    /**
     * @return \rtens\scrut\Test
     * @throws \Exception
     * @internal param TestSuiteFactory $factory
     */
    public function getTest() {
        return $this->buildTestSuite($this->get('suite', ['name' => 'Test']));
    }

    private function buildTestSuite($suiteConfig, TestName $parent = null) {
        $suiteGenerators = [
            'file' => function ($file) use ($parent) {
                return new FileTestSuite($this->getTestSuiteFactory(), $this->getFilter(), $this->fullPath(), $file, $parent);
            },
            'class' => function ($class) use ($parent) {
                return $this->getTestSuiteFactory()->getTestSuite($class, $this->getFilter(), $parent);
            },
            'name' => function ($name) use ($parent, $suiteConfig) {
                $suite = new GenericTestSuite($name, $parent);

                foreach ($this->getIn($suiteConfig, 'suites', []) as $child) {
                    $suite->add($this->buildTestSuite($child, $suite->getName()));
                }

                return $suite;
            }
        ];

        foreach ($suiteGenerators as $key => $generate) {
            if (array_key_exists($key, $suiteConfig)) {
                return $generate($suiteConfig[$key]);
            }
        }

        throw new \Exception('Invalid suite configuration');
    }

    public function fullPath($path = '') {
        return $this->workingDirectory . ($path ? (DIRECTORY_SEPARATOR . $path) : '');
    }

    private function get($path, $default = null) {
        return $this->getIn($this->config, $path, $default);
    }

    private function getIn($config, $path, $default = null) {
        foreach (explode('/', $path) as $key) {
            if (array_key_exists($key, $config)) {
                $config = $config[$key];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
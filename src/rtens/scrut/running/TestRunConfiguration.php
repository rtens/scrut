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

    private static $linkedFiles = [];

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

        if ($filterConfig = $this->get('filter')) {
            if (is_string($filterConfig)) {
                $filterConfig = [
                    'class' => $filterConfig
                ];
            }

            if ($classFilter = $this->getIn($filterConfig, 'class')) {
                if (is_string($classFilter)) {
                    $classFilter = [
                        'name' => $classFilter
                    ];
                }
                if ($name = $this->getIn($classFilter, 'name')) {
                    $filter->filterClass(function (\ReflectionClass $class) use ($name) {
                        return preg_match($name, $class->getShortName());
                    });
                }
                if ($subclass = $this->getIn($classFilter, 'subclass')) {
                    $filter->filterClass(function (\ReflectionClass $class) use ($subclass) {
                        return $class->isSubclassOf($subclass);
                    });
                }
            }
            if ($fileFilter = $this->getIn($filterConfig, 'file')) {
                $filter->filterFile(function ($path) use ($fileFilter) {
                    return preg_match($fileFilter, $path);
                });
            }
            if ($methodFilter = $this->getIn($filterConfig, 'method')) {
                if (is_string($methodFilter)) {
                    $methodFilter = [
                        'name' => $methodFilter
                    ];
                }

                if ($name = $this->getIn($methodFilter, 'name')) {
                    $filter->filterMethod(function (\ReflectionMethod $method) use ($name) {
                        return preg_match($name, $method->getName());
                    });
                }
                if ($annotation = $this->getIn($methodFilter, 'annotation')) {
                    $filter->filterMethod(function (\ReflectionMethod $method) use ($annotation) {
                        return preg_match($annotation, $method->getDocComment());
                    });
                }
            }
        }

        return $filter;
    }

    public function getTestSuiteFactory() {
        return $this->factory->getInstance($this->get('factory', TestSuiteFactory::class));
    }

    /**
     * @return \rtens\scrut\Test
     * @throws \Exception
     * @internal param TestSuiteFactory $factory
     */
    public function getTest() {
        return $this->buildTestSuite($this->get('suite', ['name' => 'Test']));
    }

    protected function buildTestSuite($suiteConfig, TestName $parent = null) {
        if (is_string($suiteConfig)) {
            $fullPath = $this->fullPath($suiteConfig);
            if (!file_exists($fullPath)) {
                throw new \Exception("Configuration file [$suiteConfig] does no exist.");
            }
            if (in_array($fullPath, self::$linkedFiles)) {
                throw new \Exception("Configuration file loop detected while linking to [$suiteConfig]");
            }
            self::$linkedFiles[] = $fullPath;
            $factory = new Factory();
            $reader = new ConfigurationReader($this->workingDirectory, $factory);
            return new LinkedTestSuite(new LinkedConfiguration($factory, $reader->read($suiteConfig), $parent));
        }

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

    protected function get($path, $default = null) {
        return $this->getIn($this->config, $path, $default);
    }

    protected function getIn($config, $path, $default = null) {
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
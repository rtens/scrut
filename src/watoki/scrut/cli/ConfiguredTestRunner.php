<?php
namespace watoki\scrut\cli;

use watoki\scrut\tests\DirectoryTestSuite;

class ConfiguredTestRunner extends DefaultTestRunner {

    private $config;

    function __construct($workingDirectory, array $config) {
        parent::__construct($workingDirectory);
        $this->config = $config;
    }

    protected function getTest() {
        if ($this->exists("suite")) {
            return $this->buildSuite($this->get("suite"));
        } else {
            return parent::getTest();
        }
    }

    public function getName() {
        return $this->get('name', parent::getName());
    }

    private function get($key, $default = null) {
        return ($this->exists($key)) ? $this->config[$key] : $default;
    }

    private function exists($key) {
        return array_key_exists($key, $this->config);
    }

    /**
     * @param $config
     * @throws \Exception
     * @return \watoki\scrut\tests\TestSuite
     */
    private function buildSuite($config) {
        if (array_key_exists('directory', $config)) {
            $suite = new DirectoryTestSuite($this->cwd($config['directory']));

            if (array_key_exists('filter', $config)) {
                $suite->setClassFilter(function (\ReflectionClass $class) use ($config) {
                    return preg_match($config['filter'], $class->getShortName());
                });
            }

            return $suite;
        }
        throw new \Exception("Could not build test suite from " . json_encode($config));
    }

} 
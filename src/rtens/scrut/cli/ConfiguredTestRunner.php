<?php
namespace rtens\scrut\cli;

use rtens\scrut\tests\file\FileTestSuite;

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

    private function get($key, $default = null) {
        return ($this->exists($key)) ? $this->config[$key] : $default;
    }

    private function exists($key) {
        return array_key_exists($key, $this->config);
    }

    /**
     * @param $config
     * @throws \Exception
     * @return \rtens\scrut\tests\TestSuite
     */
    private function buildSuite($config) {
        if (array_key_exists('file', $config)) {
            return new FileTestSuite($this->createFilter(), $this->cwd($config['file']), null);
        }
        throw new \Exception("Could not build test suite from " . json_encode($config));
    }

    protected function createFilter() {
        $filter = parent::createFilter();

        if (array_key_exists('filter', $this->config)) {
            $filter->filterClass(function (\ReflectionClass $class) {
                return preg_match($this->config['filter'], $class->getShortName());
            });
        }

        return $filter;
    }

} 
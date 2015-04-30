<?php
namespace watoki\scrut\cli;

use watoki\scrut\tests\file\FileTestSuite;

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
        if (array_key_exists('file', $config)) {
            $suite = new FileTestSuite($this->cwd($config['file']), null);

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
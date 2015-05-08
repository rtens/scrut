<?php
namespace rtens\scrut\running;

use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\TestRunListener;
use watoki\factory\Factory;

class LinkedConfiguration extends TestRunConfiguration {

    /** @var \rtens\scrut\running\TestRunConfiguration */
    private $config;

    /** @var Test */
    private $test;

    /** @var TestRunListener */
    private $listener;

    /** @var TestName */
    private $parent;

    function __construct(Factory $factory, TestRunConfiguration $config, TestName $parent) {
        $factory->setSingleton(get_class($config), $this);
        $this->config = $config;
        $this->parent = $parent;
    }

    public function getRunner() {
        return $this->config->getRunner();
    }

    public function getListeners() {
        return $this->listener ? [$this->listener] : [];
    }

    public function getFilter() {
        return $this->config->getFilter();
    }

    public function getTestSuiteFactory() {
        return $this->config->getTestSuiteFactory();
    }

    public function getTest() {
        if (!$this->test) {
            $this->test = $this->config->buildTestSuite($this->config->get('suite', ['name' => 'Test']), $this->parent);;
        }
        return $this->test;
    }

    public function fullPath($path = '') {
        return $this->config->fullPath($path);
    }

    /**
     * @param \rtens\scrut\TestRunListener $listener
     */
    public function setListener($listener) {
        $this->listener = $listener;
    }
}
<?php
namespace rtens\scrut\running;

use rtens\scrut\Test;
use rtens\scrut\TestRunListener;

class LinkedTestSuite extends Test {

    /** @var LinkedConfiguration */
    private $config;

    public function __construct(LinkedConfiguration $config) {
        parent::__construct();
        $this->config = $config;
    }

    /**
     * @param TestRunListener $listener
     * @return void
     */
    public function run(TestRunListener $listener) {
        $this->config->setListener($listener);
        $this->config->getRunner()->run();
    }

    /**
     * @return \rtens\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return $this->config->getTest()->getFailureSourceLocator();
    }
}
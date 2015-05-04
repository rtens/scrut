<?php
namespace rtens\scrut\running;

use rtens\scrut\listeners\MultiListener;
use rtens\scrut\listeners\ResultListener;
use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\TestSuite;

class TestRunner {

    /** @var TestRunConfiguration */
    private $configuration;

    /**
     * @param TestRunConfiguration $configuration <-
     */
    function __construct(TestRunConfiguration $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * @param TestName $name
     * @return bool Whether the run passed without failures
     */
    public function run(TestName $name = null) {
        $result = new ResultListener();

        $this->getTest($name)->run($this->createListener($result));

        return !$result->hasFailed();
    }

    private function getTest(TestName $name = null) {
        $test = $this->configuration->getTest();
        if ($name) {
            return $this->determineTestToRun($name, $test);
        }
        return $test;
    }

    private function createListener($result) {
        $multiListener = new MultiListener();

        foreach ($this->configuration->getListeners() as $listener) {
            $multiListener->add($listener);
        }

        return $multiListener
            ->add($result);
    }

    private function determineTestToRun(TestName $name, $test) {
        try {
            return $this->resolveTest($test, $name);
        } catch (\InvalidArgumentException $ignored) {
            return $this->determineTestSuite($test, $name);
        }
    }

    private function resolveTest(Test $root, TestName $name) {
        if ($name == $root->getName()) {
            return $root;
        }

        if ($root instanceof TestSuite) {
            foreach ($root->getTests() as $test) {
                try {
                    return $this->resolveTest($test, $name);
                } catch (\InvalidArgumentException $ignored) {
                }
            }
        }

        throw new \InvalidArgumentException("Could not resolve [$name]");
    }

    private function determineTestSuite(Test $test, TestName $name) {
        $filter = $this->configuration->getFilter();
        $factory = $this->configuration->getTestSuiteFactory();

        $first = $name->part(0);
        if (class_exists($first)) {
            $test = $factory->getTestSuite($first, $filter);
        }

        $file = $this->configuration->fullPath($first);
        if (file_exists($file)) {
            $test = new FileTestSuite($factory, $filter, $this->configuration->fullPath(), $first);
        }

        return $this->resolveTest($test, $name);
    }

} 
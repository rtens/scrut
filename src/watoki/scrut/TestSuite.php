<?php
namespace watoki\scrut;

abstract class TestSuite {

    abstract public function run(ScrutinizeListener $listener);

    abstract public function name();

    protected function runTest(ScrutinizeListener $listener, $testName, callable $test) {
        $name = $this->name() . '::' . $testName;

        $listener->started($name);

        $caught = null;
        try {
            $test();
        } catch (\Exception $e) {
            $caught = $e;
        }

        if ($caught) {
            $result = new FailedTestResult($caught);
        } else {
            $result = new PassedTestResult();
        }

        $listener->finished($name, $result);
    }
}
<?php
namespace rtens\scrut\cli;

use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\listeners\MultiListener;
use rtens\scrut\results\FailedTestResult;
use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\TestResult;
use rtens\scrut\TestRunListener;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\TestFilter;

class DefaultTestRunner implements TestRunner, TestRunListener {

    private $workingDirectory;
    private $failed = false;
    protected $factory;

    function __construct($workingDirectory) {
        $this->workingDirectory = $workingDirectory;
    }

    public function run() {
        $this->getTest()->run((new MultiListener())
            ->add($this)
            ->add($this->getListener()));
        return !$this->failed;
    }

    protected function getName() {
        return basename($this->workingDirectory);
    }

    protected function getListener() {
        return new CompactConsoleListener();
    }

    protected function cwd($path) {
        return $this->workingDirectory . DIRECTORY_SEPARATOR . $path;
    }

    protected function getTest() {
        $suite = new GenericTestSuite($this->getName(), null);
        foreach ($this->getTests($suite) as $test) {
            $suite->add($test);
        }
        return $suite;
    }

    private function getTests(Test $parent) {
        $tests = [];

        foreach (['test', 'tests', 'spec'] as $dir) {
            $dir = $this->cwd($dir);

            if (file_exists($dir)) {
                $tests[] = new FileTestSuite($this->createFilter(), $dir, $parent->getName());
            }
        }

        return $tests;
    }

    public function onResult(TestName $test, TestResult $result) {
        $this->failed = $this->failed || $result instanceof FailedTestResult;
    }

    public function onStarted(TestName $test) {
    }

    public function onFinished(TestName $test) {
    }

    /**
     * @return TestFilter
     */
    protected function createFilter() {
        return new TestFilter();
    }
}
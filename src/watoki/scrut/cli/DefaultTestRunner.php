<?php
namespace watoki\scrut\cli;

use watoki\scrut\listeners\ConsoleListener;
use watoki\scrut\listeners\MultiListener;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\Test;
use watoki\scrut\TestName;
use watoki\scrut\TestResult;
use watoki\scrut\TestRunListener;
use watoki\scrut\tests\file\FileTestSuite;
use watoki\scrut\tests\generic\GenericTestSuite;
use watoki\scrut\tests\TestFilter;

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
        return new ConsoleListener();
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
<?php
namespace rtens\scrut\cli;

use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\listeners\MultiListener;
use rtens\scrut\listeners\ResultListener;
use rtens\scrut\Test;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\TestFilter;

class DefaultTestRunner implements TestRunner {

    private $workingDirectory;

    /** @var ResultListener */
    private $result;

    function __construct($workingDirectory) {
        $this->workingDirectory = $workingDirectory;
        $this->result = new ResultListener();
    }

    public function run() {
        $listener = (new MultiListener())
            ->add($this->result)
            ->add($this->getListener());

        $this->getTest()->run($listener);

        return !$this->result->hasFailed();
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

    /**
     * @return TestFilter
     */
    protected function createFilter() {
        return new TestFilter();
    }
}
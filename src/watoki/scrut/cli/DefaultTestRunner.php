<?php
namespace watoki\scrut\cli;

use watoki\scrut\listeners\ConsoleListener;
use watoki\scrut\tests\DirectoryTestSuite;
use watoki\scrut\tests\GenericTestSuite;

class DefaultTestRunner implements TestRunner {

    private $workingDirectory;

    function __construct($workingDirectory) {
        $this->workingDirectory = $workingDirectory;
    }

    public function run() {
        $this->getTest()->run($this->getListener());
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
        $suite = new GenericTestSuite($this->getName());
        foreach ($this->getTests() as $test) {
            $suite->add($test);
        }
        return $suite;
    }

    private function getTests() {
        $tests = [];

        foreach (['test', 'tests', 'spec'] as $dir) {
            $dir = $this->cwd($dir);

            if (file_exists($dir)) {
                $tests[] = new DirectoryTestSuite($dir);
            }
        }

        return $tests;
    }
}
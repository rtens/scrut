<?php
namespace rtens\scrut\cli;

use rtens\scrut\listeners\MultiListener;
use rtens\scrut\listeners\ResultListener;
use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestSuite;

abstract class TestRunner {

    /** @var \rtens\scrut\listeners\ResultListener */
    private $result;

    /** @var string */
    private $workingDirectory;

    function __construct($workingDirectory) {
        $this->result = new ResultListener();
        $this->workingDirectory = rtrim($workingDirectory, '/\\');
    }

    /**
     * @param TestName $name
     * @return bool Whether the run passed without failures
     */
    public function run(TestName $name = null) {
        $listener = (new MultiListener())
            ->add($this->result)
            ->add($this->getListener());

        $test = $this->getTest();

        if ($name) {
            try {
                $test = $this->resolveTest($test, $name);
            } catch (\InvalidArgumentException $ignored) {

                $first = $name->part(0);
                if (class_exists($first)) {
                    if (is_subclass_of($first, StaticTestSuite::class)) {
                        $test = new $first($this->createFilter());
                    } else {
                        $test = new PlainTestSuite($this->createFilter(), $first);
                    }
                }

                $file = $this->cwd($first);
                if (file_exists($file)) {
                    $test = new FileTestSuite($this->createFilter(), $this->cwd(''), $first);
                }

                $test = $this->resolveTest($test, $name);
            }
        }

        $test->run($listener);

        return !$this->result->hasFailed();
    }

    protected function cwd($path) {
        return $this->workingDirectory . DIRECTORY_SEPARATOR . $path;
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

    /**
     * @return \rtens\scrut\TestRunListener
     */
    abstract protected function getListener();

    /**
     * @return Test
     */
    abstract protected function getTest();

    /**
     * @return \rtens\scrut\tests\TestFilter
     */
    abstract protected function createFilter();

} 
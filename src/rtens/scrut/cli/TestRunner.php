<?php
namespace rtens\scrut\cli;

use rtens\scrut\listeners\MultiListener;
use rtens\scrut\listeners\ResultListener;
use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;

abstract class TestRunner {

    /** @var \rtens\scrut\listeners\ResultListener */
    private $result;

    function __construct() {
        $this->result = new ResultListener();
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
            $test = $this->resolveTest($test, $name);
        }

        $test->run($listener);

        return !$this->result->hasFailed();
    }

    private function resolveTest(Test $root, TestName $name) {
        if ($name == $root->getName()) {
            return $root;
        }

        $class = $name->part(0);
        if (class_exists($class)) {
            if (is_subclass_of($class, StaticTestSuite::class)) {
                return new $class($this->createFilter());
            }
            return new PlainTestSuite($this->createFilter(), $class);
        }

        if ($root instanceof GenericTestSuite) {
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
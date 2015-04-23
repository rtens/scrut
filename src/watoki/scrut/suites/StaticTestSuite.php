<?php
namespace watoki\scrut\suites;

use watoki\scrut\failures\EmptyTestSuiteFailure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\failures\NotEqualFailure;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\ScrutinizeListener;
use watoki\scrut\TestSuite;

abstract class StaticTestSuite extends TestSuite {

    protected function before() {
    }

    protected function after() {
    }

    public function run(ScrutinizeListener $listener) {
        $class = new \ReflectionClass($this);
        $methods = [];
        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass() == $class && $method->isPublic() && !$method->isStatic()) {
                $methods[] = $method->getName();
            }
        }

        if (!$methods) {
            $listener->onTestFinished($this->name(), new IncompleteTestResult(new EmptyTestSuiteFailure($this)));
        }

        foreach ($methods as $methodName) {
            $this->runTest($listener, $methodName, function () use ($methodName) {
                $this->before();
                $this->$methodName();
                $this->after();
            });
        }
    }

    protected function name() {
        return get_class($this);
    }

    protected function assert($value, $equals = true, $message = null) {
        if ($value !== $equals) {
            throw new NotEqualFailure($value, $equals, $message);
        }
    }

    protected function markIncomplete($message = "") {
        throw new IncompleteTestFailure($message);
    }
}
<?php
namespace watoki\scrut\suites;

use watoki\scrut\Asserter;
use watoki\scrut\failures\EmptyTestSuiteFailure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\ScrutinizeListener;
use watoki\scrut\TestSuite;

abstract class StaticTestSuite extends TestSuite {

    /** @var Asserter */
    protected $assert;

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
            $this->runTest($listener, $methodName, function (Asserter $assert) use ($methodName) {
                $this->assert = $assert;

                $this->before();
                $this->$methodName();
                $this->after();
            });
        }
    }

    public function name() {
        return get_class($this);
    }

    protected function assert($value, $message = null) {
        $this->assert->__invoke($value, $message);
    }

    protected function markIncomplete($message = "") {
        throw new IncompleteTestFailure($message);
    }
}
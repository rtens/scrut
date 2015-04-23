<?php
namespace watoki\scrut\suites;

use watoki\scrut\failures\NotEqualFailure;
use watoki\scrut\ScrutinizeListener;
use watoki\scrut\TestSuite;

abstract class StaticTestSuite extends TestSuite {

    protected function before() {
    }

    protected function after() {
    }

    public function run(ScrutinizeListener $listener) {
        $class = new \ReflectionClass($this);
        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass() == $class && $method->isPublic() && !$method->isStatic()) {
                $this->runTest($listener, $method->getName(), function () use ($method) {
                    $this->before();
                    $methodName = $method->getName();
                    $this->$methodName();
                    $this->after();
                });
            }
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
}
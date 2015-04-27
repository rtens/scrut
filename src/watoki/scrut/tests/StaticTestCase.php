<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;

class StaticTestCase extends PlainTestCase {

    protected function execute(Asserter $assert) {
        $class = $this->method->getDeclaringClass();
        if (!$class->isSubclassOf(StaticTestSuite::class)) {
            throw new \InvalidArgumentException("Not a StaticTestSuite: [{$class->getName()}]");
        }

        /** @var StaticTestSuite $suite */
        $suite = $class->newInstanceArgs();
        $suite->execute($this->method->getName(), $assert);
    }

    protected function getExceptionSourceFromTrace($trace) {
        foreach ($trace as $i => $step) {
            if ($step['class'] == StaticTestSuite::class && $step['function'] == 'execute') {
                return $this->formatStep($trace[$i - 2]);
            }
        }

        return 'unknown location';
    }
}
<?php
namespace rtens\scrut\tests\statics;

use rtens\scrut\Assert;
use rtens\scrut\tests\plain\PlainTestCase;
use rtens\scrut\tests\TestFilter;

class StaticTestCase extends PlainTestCase {

    protected function execute(Assert $assert) {
        $class = $this->method->getDeclaringClass();
        if (!$class->isSubclassOf(StaticTestSuite::class)) {
            throw new \InvalidArgumentException("Not a StaticTestSuite: [{$class->getName()}]");
        }

        /** @var StaticTestSuite $suite */
        $suite = $class->newInstance(new TestFilter());
        $suite->execute($this->method->getName(), $assert);
    }

    protected function getFailureSourceLocator() {
        return new StaticFailureSourceLocator($this->method);
    }
}
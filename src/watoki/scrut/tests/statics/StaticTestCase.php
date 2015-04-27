<?php
namespace watoki\scrut\tests\statics;

use watoki\scrut\Asserter;
use watoki\scrut\tests\plain\PlainTestCase;

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

    public function getFailureSourceLocator() {
        return new StaticFailureSourceLocator($this->method);
    }
}
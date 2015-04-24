<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;

class StaticTestCase extends PlainTestCase {

    protected function execute(Asserter $assert) {
        $class = $this->getMethod()->getDeclaringClass();
        if (!$class->isSubclassOf(StaticTestSuite::class)) {
            throw new \InvalidArgumentException("Not a StaticTestSuite: [{$class->getName()}]");
        }

        /** @var StaticTestSuite $suite */
        $suite = $class->newInstanceArgs();
        $suite->execute($this->getMethod()->getName(), $assert);
    }
}
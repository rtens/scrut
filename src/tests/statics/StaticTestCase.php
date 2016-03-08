<?php
namespace rtens\scrut\tests\statics;

use rtens\scrut\Assert;
use rtens\scrut\tests\plain\PlainTestCase;
use rtens\scrut\tests\TestFilter;

class StaticTestCase extends PlainTestCase {

    protected function execute(Assert $assert) {
        if (!$this->class->isSubclassOf(StaticTestSuite::class)) {
            throw new \InvalidArgumentException("Not a StaticTestSuite: [{$this->class->getName()}]");
        }

        /** @var StaticTestSuite $suite */
        $factory = $this->createFactory($assert);
        $suite = $factory->getInstance($this->class->getName(), [
            'filter' => new TestFilter()
        ]);

        $this->callFixtureHook(self::BEFORE_METHOD);
        $suite->execute($this->method->getName(), $assert);
        $this->callFixtureHook(self::AFTER_METHOD);
    }

    protected function getFailureSourceLocator() {
        return new StaticFailureSourceLocator($this->method);
    }
}
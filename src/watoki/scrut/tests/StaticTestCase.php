<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;

class StaticTestCase extends TestCase {

    /** @var \ReflectionMethod */
    private $method;

    function __construct(\ReflectionMethod $method) {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->method->getName();
    }

    protected function execute(Asserter $assert) {
        $class = $this->method->getDeclaringClass();
        if (!$class->isSubclassOf(StaticTestSuite::class)) {
            throw new \InvalidArgumentException("Not a StaticTestSuite: [{$class->getName()}]");
        }

        /** @var StaticTestSuite $suite */
        $suite = $class->newInstanceArgs();
        $suite->execute($this->method->getName(), $assert);
    }
}
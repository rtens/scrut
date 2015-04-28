<?php
namespace watoki\scrut\tests\plain;

use watoki\scrut\Asserter;
use watoki\scrut\TestName;
use watoki\scrut\tests\TestCase;

class PlainTestCase extends TestCase {

    /** @var \ReflectionMethod */
    protected $method;

    function __construct(\ReflectionMethod $method, TestName $parent = null) {
        parent::__construct($parent);
        $this->method = $method;
    }

    /**
     * @return TestName
     */
    public function getName() {
        return parent::getName()->with($this->method->getName());
    }

    protected function execute(Asserter $assert) {
        $class = $this->method->getDeclaringClass();

        $suite = $class->newInstanceArgs();

        if (method_exists($suite, 'before')) {
            if (!is_callable([$suite, 'before'])) {
                throw new \ReflectionException("Method [" . $class->getName() . '::before] must be public');
            }
            $suite->before();
        }

        try {
            $this->method->invoke($suite, $assert);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            if (method_exists($suite, 'after')) {
                if (!is_callable([$suite, 'after'])) {
                    throw new \ReflectionException("Method [" . $class->getName() . '::after] must be public');
                }
                $suite->after();
            }
        }
    }

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new PlainFailureSourceLocator($this->method);
    }
}
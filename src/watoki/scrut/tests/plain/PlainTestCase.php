<?php
namespace watoki\scrut\tests\plain;

use watoki\factory\Factory;
use watoki\scrut\Asserter;
use watoki\scrut\TestName;
use watoki\scrut\tests\TestCase;

class PlainTestCase extends TestCase {

    /** @var \ReflectionMethod */
    protected $method;

    /**
     * @param \ReflectionMethod $method
     * @param TestName $parent
     */
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

        $factory = new Factory();
        $factory->setSingleton(Asserter::class, $assert);
        $suite = $factory->getInstance($class->getName());

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
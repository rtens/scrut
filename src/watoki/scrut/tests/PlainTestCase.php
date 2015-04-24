<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;

class PlainTestCase extends TestCase {

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
        $class = $this->getMethod()->getDeclaringClass();

        $suite = $class->newInstanceArgs();

        if (method_exists($suite, 'before')) {
            if (!is_callable([$suite, 'before'])) {
                throw new \ReflectionException("Method [" . $class->getName() . '::before] must be public');
            }
            $suite->before();
        }

        try {
            $this->getMethod()->invoke($suite, $assert);
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
     * @return \ReflectionMethod
     */
    public function getMethod() {
        return $this->method;
    }
}
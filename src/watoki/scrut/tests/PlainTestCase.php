<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;

class PlainTestCase extends TestCase {

    /** @var \ReflectionMethod */
    protected $method;

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

    protected function getNoAssertionsFailureSource() {
        $method = $this->method;
        return $this->formatFileAndLine($method->getFileName(), $method->getStartLine());
    }

    protected function getExceptionSourceFromTrace($trace) {
        foreach ($trace as $i => $step) {
            if (!isset($step['file'])) {
                return $this->formatStep($trace[$i - 1]);
            }
        }

        return 'unknown location';
    }
}
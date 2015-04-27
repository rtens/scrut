<?php
namespace watoki\scrut\tests\plain;

use watoki\scrut\tests\FailureSourceLocator;

class PlainFailureSourceLocator extends FailureSourceLocator {

    /** @var \Reflector|\ReflectionMethod|\ReflectionClass */
    private $reflection;

    function __construct(\Reflector $reflection) {
        $this->reflection = $reflection;
    }

    protected function getEmptyTestSuiteFailureSource() {
        return $this->reflection->getFileName() . ':' . $this->reflection->getStartLine();
    }

    protected function getNoAssertionsFailureSource() {
        return FailureSourceLocator::formatFileAndLine($this->reflection->getFileName(), $this->reflection->getStartLine());
    }

    protected function getExceptionSourceFromTrace($trace) {
        foreach ($trace as $i => $step) {
            if (!isset($step['file'])) {
                return $this->formatStep($trace[$i - 1]);
            }
        }

        return $this->unknownSource();
    }
}
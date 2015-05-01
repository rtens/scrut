<?php
namespace watoki\scrut\tests\plain;

use watoki\scrut\tests\FailureSourceLocator;

class PlainFailureSourceLocator extends FailureSourceLocator {

    /** @var \Reflector|\ReflectionMethod|\ReflectionClass */
    private $reflection;

    function __construct(\Reflector $reflection) {
        $this->reflection = $reflection;
    }

    public function locateEmptyTestFailureSource() {
        return FailureSourceLocator::formatFileAndLine($this->reflection->getFileName(), $this->reflection->getStartLine());
    }

    protected function getExceptionSourceFromTrace($trace) {
        $candidate = [];
        foreach ($trace as $i => $step) {
            if (isset($step['class']) && $step['class'] == PlainTestCase::class && $step['function'] == 'execute') {
                return $this->formatStep($candidate);
            }
            if ($i > 0 && isset($trace[$i - 1]['file'])) {
                $candidate = $trace[$i - 1];
            }
        }

        return $this->unknownSource();
    }
}
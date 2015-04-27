<?php
namespace watoki\scrut\tests\generic;

use watoki\scrut\tests\FailureSourceLocator;

class GenericFailureSourceLocator extends FailureSourceLocator {

    private $creation;

    function __construct(\Exception $creation) {
        $this->creation = $creation;
    }

    protected function getEmptyTestSuiteFailureSource() {
        $creation = $this->creation->getTrace()[0];
        return $creation['file'] . ':' . $creation['line'];
    }

    protected function getNoAssertionsFailureSource() {
        $creation = $this->creation->getTrace()[0];
        return FailureSourceLocator::formatFileAndLine($creation['file'], $creation['line']);
    }

    protected function getExceptionSourceFromTrace($trace) {
        foreach ($trace as $i => $step) {
            if (!isset($step['file'])) {
                return $this->formatStep($trace[$i - 1]);
            } else if (isset($step['class']) && $step['class'] == GenericTestCase::class && $step['function'] == 'execute') {
                return $this->formatStep($trace[$i - 2]);
            }
        }

        return $this->unknownSource();
    }
}
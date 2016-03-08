<?php
namespace rtens\scrut\tests\statics;

use rtens\scrut\tests\plain\PlainFailureSourceLocator;

class StaticFailureSourceLocator extends PlainFailureSourceLocator {

    protected function getExceptionSourceFromTrace($trace) {
        foreach ($trace as $i => $step) {
            if (isset($step['class']) && $step['class'] == StaticTestSuite::class && $step['function'] == 'execute') {
                return $this->formatStep($trace[$i - 2]);
            }
        }

        return $this->unknownSource();
    }
}
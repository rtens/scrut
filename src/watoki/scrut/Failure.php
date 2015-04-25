<?php
namespace watoki\scrut;

use watoki\scrut\tests\GenericTestCase;
use watoki\scrut\tests\StaticTestSuite;

class Failure extends \RuntimeException {

    public function __construct($message = null) {
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getFailureMessage() {
        return "";
    }

    /**
     * @internal param TestSuite $suite The suite of the test that caused the Failure
     * @return string Containing file and line number
     */
    public function getLocation() {
        return $this->findLocation($this);
    }

    /**
     * @param \Exception $of
     * @return string
     */
    protected function findLocation(\Exception $of) {
        $first = [
            'file' => $of->getFile(),
            'line' => $of->getLine(),
            'class' => null
        ];

        $trace = array_merge([$first], $of->getTrace());
        foreach ($trace as $i => $step) {
            if (!isset($step['file'])) {
                return $this->formatStep($trace[$i - 1]);
            } else if ($step['class'] == StaticTestSuite::class && $step['function'] == 'execute') {
                return $this->formatStep($trace[$i - 2]);
            } else if ($step['class'] == GenericTestCase::class && $step['function'] == 'execute') {
                return $this->formatStep($trace[$i - 2]);
            }
        }

        return 'unknown location';
    }

    private function formatStep($step) {
        return $this->formatFileAndLine($step['file'], $step['line']);
    }

    /**
     * @param string $file
     * @param int $line
     * @return string
     */
    protected function formatFileAndLine($file, $line) {
        return $file . '(' . $line . ')';
    }
}
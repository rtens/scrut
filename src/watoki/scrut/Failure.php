<?php
namespace watoki\scrut;

use watoki\scrut\tests\GenericTestCase;
use watoki\scrut\tests\StaticTestSuite;

class Failure extends \RuntimeException {

    /** @var string */
    private $failureMessage;

    /** @var string */
    private $location;

    public function __construct($failureMessage = "", $userMessage = "", $location = null) {
        parent::__construct($userMessage);
        $this->failureMessage = $failureMessage;
        $this->location = $location ?: $this->findLocation($this);
    }

    /**
     * @return string
     */
    final public function getFailureMessage() {
        return $this->failureMessage;
    }

    /**
     * @internal param TestSuite $suite The suite of the test that caused the Failure
     * @return string Containing file and line number
     */
    final public function getLocation() {
        return $this->location;
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
<?php
namespace watoki\scrut;

use watoki\scrut\tests\GenericTestCase;
use watoki\scrut\tests\TestSuite;

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
     * @param TestSuite $suite
     * @return string Containing file and line number
     */
    public function getLocation(TestSuite $suite) {
        return $this->findLocation($suite, $this);
    }

    /**
     * @param TestSuite $suite
     * @return string
     */
    protected function findLocation(TestSuite $suite, \Exception $e) {
        $last = [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];

        $candidates = [$last];

        $generic = false;
        foreach ($e->getTrace() as $step) {
            $generic = $generic || $step['class'] == GenericTestCase::class;

            if ($generic && substr($step['class'], 0, 13) != 'watoki\\scrut\\'
                || !$generic && $step['class'] == get_class($suite)
            ) {
                $candidates[] = $last;
            }

            $last = $step;
        }

        if (!$candidates) {
            return 'unknown location';
        } else {
            $step = array_pop($candidates);
            return $step['file'] . '(' . $step['line'] . ')';
        }
    }
}
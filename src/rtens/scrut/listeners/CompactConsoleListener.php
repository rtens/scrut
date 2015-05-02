<?php
namespace rtens\scrut\listeners;

use rtens\scrut\results\FailedTestResult;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\results\NotPassedTestResult;
use rtens\scrut\results\PassedTestResult;
use rtens\scrut\TestName;
use rtens\scrut\TestResult;

class CompactConsoleListener extends ConsoleListener {

    /**
     * @param string|object $result e.g. 'PassedTestResult'
     * @return string e.g. 'Passed'
     */
    protected static function shortResultClassName($result) {
        $result = is_object($result) ? get_class($result) : $result;
        return substr($result, strrpos($result, '\\') + 1, -10);
    }

    public function onResult(TestName $test, TestResult $result) {
        parent::onResult($test, $result);

        if ($result instanceof IncompleteTestResult) {
            $this->print_('I');
        } else if ($result instanceof FailedTestResult) {
            $this->print_('F');
        } else if ($result instanceof PassedTestResult) {
            $this->print_('.');
        } else {
            $this->print_('?');
        }
    }

    protected function onEnd() {
        $this->printLine();

        $maxLevel = -1;
        $counts = [];

        foreach (self::$RESULT_CLASSES as $level => $resultClass) {
            $results = $this->getResults($resultClass);

            if ($results) {
                $type = self::shortResultClassName($resultClass);

                $maxLevel = max($maxLevel, $level);
                $counts[] = count($results) . ' ' . $type;

                $this->printResultClass($type, $results);
            }
        }

        $results = [
            -1 => 'Unknown result',
            '=D',
            '=|',
            '=('
        ];

        $this->printLine();
        $this->printLine($results[$maxLevel] . ' ' . implode(', ', $counts));
    }

    protected function printResultClass($type, $results) {
        if ($type == 'Passed') {
            return;
        }

        $this->printLine();
        $this->printLine('---- ' . $type . ' ----');

        foreach ($results as $name => $result) {
            $this->printResult($name, $result);
        }
    }

    protected function printResult($testName, $result) {
        if ($result instanceof NotPassedTestResult) {
            $this->printNotPassedResult($testName, $result);
        } else {
            $this->printLine($testName);
        }
    }

    protected function printNotPassedResult($testName, NotPassedTestResult $result) {
        $this->printLine($testName . ' [' . $result->getFailure()->getFailureSource() . ']');
        $this->printNotEmptyLine('    ' . $result->getFailure()->getFailureMessage());
        $this->printNotEmptyLine('    ' . $result->getFailure()->getMessage());
    }

}
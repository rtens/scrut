<?php
namespace watoki\scrut\listeners;

use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\NotPassedTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\TestName;
use watoki\scrut\TestResult;

class CompactConsoleListener extends ConsoleListener {

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

        $maxLevel = 0;
        $counts = [];

        foreach (self::$RESULT_CLASSES as $level => $resultClass) {
            $name = substr($resultClass, 21, -10);
            $results = $this->getResults($resultClass);

            if (!$results) {
                continue;
            }

            $maxLevel = max($maxLevel, $level);
            $counts[] = count($results) . ' ' . $name;

            if ($resultClass == PassedTestResult::class) {
                continue;
            }

            $this->printLine();
            $this->printLine('---- ' . $name . ' ----');

            foreach ($results as $name => $result) {
                if ($result instanceof NotPassedTestResult) {
                    $this->printLine($name . ' [' . $result->getFailure()->getFailureSource() . ']');
                    $this->printNotEmptyLine('    ' . $result->getFailure()->getFailureMessage());
                    $this->printNotEmptyLine('    ' . $result->getFailure()->getMessage());
                } else {
                    $this->printLine($name);
                }
            }
        }

        $results = [
            '=D',
            '=|',
            '=('
        ];

        $this->printLine();
        $this->printLine($results[$maxLevel] . ' ' . implode(', ', $counts));
    }

}
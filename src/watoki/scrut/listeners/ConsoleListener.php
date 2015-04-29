<?php
namespace watoki\scrut\listeners;

use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\NotPassedTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\Test;
use watoki\scrut\TestName;
use watoki\scrut\TestResult;
use watoki\scrut\TestRunListener;

class ConsoleListener implements TestRunListener {

    /** @var array|array[]|TestResult[][] */
    private $results = [];

    /** @var string[] The names of all started but not finished Tests */
    private $running;

    public function onStarted(TestName $test) {
        $this->running[] = $test->toString();
    }

    public function onFinished(TestName $test) {
        $key = array_search($test->toString(), $this->running);
        if ($key !== false) {
            unset($this->running[$key]);
        }

        if ($this->running) {
            return;
        }

        $this->printLine();

        $level = 0;
        $counts = [];
        foreach ($this->results as $type => $results) {
            $name = substr($type, 21, -10);
            $counts[] = count($results) . ' ' . $name;

            if ($type == PassedTestResult::class) {
                continue;
            } else if ($type == IncompleteTestResult::class) {
                $level = max($level, 1);
            } else if ($type == FailedTestResult::class) {
                $level = max($level, 2);
            }

            $this->printLine();
            $this->printLine('---- ' . $name . ' ----');

            /** @var Test $test */
            foreach ($results as $name => list($test, $result)) {
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
            'Passed =D',
            'Incomplete =|',
            'FAILED =('
        ];

        $this->printLine();
        $this->printLine($results[$level]);
        $this->printLine(implode(', ', $counts));
    }

    public function onResult(TestName $test, TestResult $result) {
        $this->results[get_class($result)][$test->toString()] = [$test, $result];

        if ($result instanceof IncompleteTestResult) {
            $this->output('I');
        } else if ($result instanceof FailedTestResult) {
            $this->output('F');
        } else if ($result instanceof PassedTestResult) {
            $this->output('.');
        } else {
            $this->output('?');
        }
    }

    protected function printLine($text = "") {
        $this->output($text . PHP_EOL);
    }

    protected function output($text = "") {
        echo $text;
    }

    private function printNotEmptyLine($string) {
        if (!trim($string)) {
            return;
        }
        $this->printLine($string);
    }
}
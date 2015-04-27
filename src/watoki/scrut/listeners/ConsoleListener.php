<?php
namespace watoki\scrut\listeners;

use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\NotPassedTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\Test;
use watoki\scrut\TestResult;
use watoki\scrut\TestRunListener;

class ConsoleListener implements TestRunListener {

    /** @var array|Test[] */
    private $running = [];

    /** @var array|array[]|TestResult[][] */
    private $results = [];

    public function onStarted(Test $test) {
        $this->running[] = $test;
    }

    public function onFinished(Test $test) {
        array_pop($this->running);

        if ($this->running) {
            return;
        }

        $this->printLine();

        $counts = [];
        foreach ($this->results as $type => $results) {
            $name = substr($type, 21, -10);
            $counts[] = count($results) . ' ' . $name;

            if ($type == PassedTestResult::class) {
                continue;
            }

            $this->printLine();
            $this->printLine('---- ' . $name . ' ----');

            /** @var Test $test */
            foreach ($results as $name => list($test, $result)) {
                if ($result instanceof NotPassedTestResult) {
                    $this->printLine($name . ' [' . $test->getFailureSource($result->failure()) . ']');
                    $this->printNotEmptyLine('    ' . $result->failure()->getFailureMessage());
                    $this->printNotEmptyLine('    ' . $result->failure()->getMessage());
                } else {
                    $this->printLine($name);
                }
            }
        }

        $this->printLine();
        $this->printLine(implode(', ', $counts));
    }

    public function onResult(Test $test, TestResult $result) {
        $fullName = implode(array_map(function (Test $test) {
            return $test->getName();
        }, $this->running), '::');

        $this->results[get_class($result)][$fullName] = [$test, $result];

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
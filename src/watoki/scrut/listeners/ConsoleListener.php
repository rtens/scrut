<?php
namespace watoki\scrut\listeners;

use watoki\scrut\Failure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\TestRunListener;
use watoki\scrut\TestResult;

class ConsoleListener implements TestRunListener {

    /** @var Failure[] $failed */
    private $failed = [];

    /** @var array|IncompleteTestFailure[] */
    private $incomplete = [];

    public function onRunStarted() {
    }

    public function onTestStarted($name) {
    }

    public function onTestFinished($name, TestResult $result) {
        if ($result instanceof IncompleteTestResult) {
            $this->incomplete[$name] = $result->failure();
            $this->output('I');
        } else if ($result instanceof FailedTestResult) {
            $this->failed[$name] = $result->failure();
            $this->output("F");
        } else {
            $this->output(".");
        }
    }

    public function onRunFinished() {
        $this->printLine();

        if (!$this->failed && !$this->incomplete) {
            $this->printLine("All passed =)");
            return;
        }

        if ($this->failed) {
            $this->printLine();
            $this->printLine(count($this->failed) . " FAILED:");

            foreach ($this->failed as $name => $failure) {
                $this->printLine($name . ' [' . $failure->getFailureFileAndLine() . ']');

                $failureMessage = $failure->getFailureMessage();
                if ($failureMessage) {
                    $this->printLine('   ' . $failureMessage);
                }
            }
        }

        if ($this->incomplete) {
            $this->printLine();
            $this->printLine(count($this->incomplete) . " INCOMPLETE:");

            foreach ($this->incomplete as $name => $failure) {
                $this->printLine($name . ' [' . $failure->getFailureFileAndLine() . ']');

                $failureMessage = $failure->getFailureMessage();
                if ($failureMessage) {
                    $this->printLine('    ' . $failureMessage);
                }
            }
        }

        $this->printLine();
        $this->printLine(count($this->incomplete) . ' incomplete and ' . count($this->failed) . ' failed =(');
    }

    protected function printLine($text = "") {
        $this->output($text . PHP_EOL);
    }

    protected function output($text = "") {
        echo $text;
    }
}
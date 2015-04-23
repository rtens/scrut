<?php
namespace watoki\scrut\listeners;

use watoki\scrut\Failure;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\ScrutinizeListener;
use watoki\scrut\TestResult;

class ConsoleListener implements ScrutinizeListener {

    /** @var Failure[] $failed */
    private $failed = [];

    public function onRunStarted() {
    }

    public function onRunFinished() {
        $this->printLine();
        $this->printLine();

        if (count($this->failed)) {
            $this->printLine(count($this->failed) . " FAILED:");
            foreach ($this->failed as $name => $failure) {
                $this->printLine();

                $this->printLine($name . ' [' . $failure->getFailureFileAndLine() . ']');
                $this->printLine('   ' . $failure->getFailureMessage());
            }
        } else {
            $this->printLine("All passed =)");
        }
    }

    public function onTestStarted($name) {
    }

    public function onTestFinished($name, TestResult $result) {
        if ($result instanceof FailedTestResult) {
            $this->failed[$name] = $result->failure();
            echo "F";
        } else {
            echo ".";
        }
    }

    private function printLine($text = "") {
        echo $text . PHP_EOL;
    }
}
<?php
namespace watoki\scrut\listeners;

use watoki\scrut\TestName;
use watoki\scrut\TestResult;

class VerboseConsoleListener extends CompactConsoleListener {

    private $depth = 0;

    public function onStarted(TestName $test) {
        parent::onStarted($test);

        if ($this->depth) {
            $this->printLine();
        }
        $this->depth++;
        $this->print_($test->toString());
    }

    public function onResult(TestName $test, TestResult $result) {
        ConsoleListener::onResult($test, $result);

        $this->print_(' -> ' . substr(get_class($result), 21, -10));
    }

    public function onFinished(TestName $test) {
        parent::onFinished($test);

        $this->depth--;
        if (!$this->depth) {
            $this->printLine();
        }
    }
}
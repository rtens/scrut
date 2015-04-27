<?php
namespace watoki\scrut;

interface TestRunListener {

    public function onStarted(Test $test);

    public function onFinished(Test $test);

    public function onResult(Test $test, TestResult $result);
}
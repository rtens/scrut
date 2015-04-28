<?php
namespace watoki\scrut;

interface TestRunListener {

    public function onStarted(TestName $test);

    public function onFinished(TestName $test);

    public function onResult(TestName $test, TestResult $result);
}
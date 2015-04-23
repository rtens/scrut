<?php
namespace watoki\scrut;

interface TestRunListener {

    public function onStarted(Test $test);

    public function onResult(TestResult $result);

    public function onFinished(Test $test);
}
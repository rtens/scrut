<?php
namespace watoki\scrut;

interface TestRunListener {

    public function onRunStarted();

    public function onRunFinished();

    public function onTestStarted($name);

    public function onTestFinished($name, TestResult $result);
}
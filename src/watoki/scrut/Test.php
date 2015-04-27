<?php
namespace watoki\scrut;

interface Test {

    /**
     * @param TestRunListener $listener
     * @return void
     */
    public function run(TestRunListener $listener);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param Failure $failure
     * @return string Containing file name and optionally the line in test case where this exception originated
     */
    public function getFailureSource(Failure $failure);
} 
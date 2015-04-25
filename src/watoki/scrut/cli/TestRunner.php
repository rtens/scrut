<?php
namespace watoki\scrut\cli;

interface TestRunner {

    /**
     * @return bool Whether the run passed without failures
     */
    public function run();

} 
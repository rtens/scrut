<?php
namespace rtens\scrut\cli;

use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\tests\TestFilter;

abstract class DefaultTestRunner extends TestRunner {

    private $workingDirectory;

    function __construct($workingDirectory) {
        parent::__construct();
        $this->workingDirectory = $workingDirectory;
    }

    protected function getListener() {
        return new CompactConsoleListener();
    }

    protected function cwd($path) {
        return $this->workingDirectory . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @return TestFilter
     */
    protected function createFilter() {
        return new TestFilter();
    }
}
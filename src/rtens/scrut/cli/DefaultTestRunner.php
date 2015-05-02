<?php
namespace rtens\scrut\cli;

use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\tests\TestFilter;

abstract class DefaultTestRunner extends TestRunner {

    protected function getListener() {
        return new CompactConsoleListener();
    }

    /**
     * @return TestFilter
     */
    protected function createFilter() {
        return new TestFilter();
    }
}
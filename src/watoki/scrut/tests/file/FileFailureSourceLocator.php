<?php
namespace watoki\scrut\tests\file;

use watoki\scrut\tests\FailureSourceLocator;

class FileFailureSourceLocator extends FailureSourceLocator {

    private $path;

    function __construct($path) {
        $this->path = $path;
    }

    protected function getEmptyTestSuiteFailureSource() {
        return $this->path;
    }

    /**
     * @param array $trace
     * @return string
     */
    protected function getExceptionSourceFromTrace($trace) {
        return $this->unknownSource();
    }

    /**
     * @return string
     */
    protected function getNoAssertionsFailureSource() {
        return $this->unknownSource();
    }
}
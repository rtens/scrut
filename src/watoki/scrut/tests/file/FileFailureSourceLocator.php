<?php
namespace watoki\scrut\tests\file;

use watoki\scrut\tests\FailureSourceLocator;

class FileFailureSourceLocator extends FailureSourceLocator {

    private $path;

    function __construct($path) {
        $this->path = $path;
    }

    public function locateEmptyTestSuiteFailureSource() {
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
    public function locatorNoAssertionsFailureSource() {
        return $this->unknownSource();
    }
}
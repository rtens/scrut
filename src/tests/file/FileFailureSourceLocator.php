<?php
namespace rtens\scrut\tests\file;

use rtens\scrut\tests\FailureSourceLocator;

class FileFailureSourceLocator extends FailureSourceLocator {

    private $path;

    function __construct($path) {
        $this->path = $path;
    }

    public function locateEmptyTestFailureSource() {
        return $this->path;
    }

    /**
     * @param array $trace
     * @return string
     */
    protected function getExceptionSourceFromTrace($trace) {
        return $this->unknownSource();
    }
}
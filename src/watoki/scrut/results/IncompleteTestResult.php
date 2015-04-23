<?php
namespace watoki\scrut\results;

use watoki\scrut\failures\IncompleteTestFailure;

class IncompleteTestResult extends FailedTestResult {

    function __construct(IncompleteTestFailure $failure) {
        parent::__construct($failure);
    }

    /**
     * @return IncompleteTestFailure
     */
    public function failure() {
        return parent::failure();
    }

}
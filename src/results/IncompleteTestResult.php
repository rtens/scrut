<?php
namespace rtens\scrut\results;

use rtens\scrut\failures\IncompleteTestFailure;

class IncompleteTestResult extends NotPassedTestResult {

    function __construct(IncompleteTestFailure $failure) {
        parent::__construct($failure);
    }

    /**
     * @return IncompleteTestFailure
     */
    public function getFailure() {
        return parent::getFailure();
    }

}
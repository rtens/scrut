<?php
namespace rtens\scrut\failures;

use rtens\scrut\Failure;

class IncompleteTestFailure extends Failure {

    public function __construct($failureMessage = "") {
        parent::__construct($failureMessage, "");
    }

}
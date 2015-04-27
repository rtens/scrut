<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;

class IncompleteTestFailure extends Failure {

    public function __construct($failureMessage = "") {
        parent::__construct($failureMessage, "");
    }

}
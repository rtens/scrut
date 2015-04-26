<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;

class IncompleteTestFailure extends Failure {

    public function __construct($failureMessage = "", $location = null) {
        parent::__construct($failureMessage, "", $location);
    }

}
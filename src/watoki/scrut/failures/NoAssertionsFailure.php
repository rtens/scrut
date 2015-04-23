<?php
namespace watoki\scrut\failures;

class NoAssertionsFailure extends IncompleteTestFailure {

    public function getFailureMessage() {
        return "No assertions made";
    }
}
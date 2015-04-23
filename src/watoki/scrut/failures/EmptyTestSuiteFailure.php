<?php
namespace watoki\scrut\failures;

class EmptyTestSuiteFailure extends IncompleteTestFailure {

    public function getFailureMessage() {
        return "Empty test suite";
    }

}
<?php
namespace watoki\scrut;

use watoki\scrut\failures\NotEqualFailure;
use watoki\scrut\failures\NotTrueFailure;

class Asserter {

    function __invoke($value, $message = "") {
        $this->isTrue($value, $message);
    }

    public function isTrue($value, $message = "") {
        if ($value !== true) {
            throw new NotTrueFailure($value, $message);
        }
    }

    public function equals($value, $expected, $message = "") {
        if ($value !== $expected) {
            throw new NotEqualFailure($value, $expected, $message);
        }
    }

}
<?php
namespace watoki\scrut;

class AsserterProxy extends Asserter {

    private $hasCalls = false;

    public function isTrue($value, $message = "") {
        $this->addCall();
        parent::isTrue($value, $message);
    }

    public function equals($value, $expected, $message = "") {
        $this->addCall();
        parent::equals($value, $expected, $message);
    }

    public function hasCalls() {
        return $this->hasCalls;
    }

    private function addCall() {
        $this->hasCalls = true;
    }
}
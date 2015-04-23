<?php
namespace watoki\scrut;

class Failure extends \RuntimeException {

    public function __construct($message = null) {
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getFailureMessage() {
        return "";
    }

    /**
     * @return string Containing file and line number
     */
    public function getLocation() {
        return 'unknown location';
    }
}
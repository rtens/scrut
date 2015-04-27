<?php
namespace watoki\scrut;

class Failure extends \RuntimeException {

    /** @var string */
    private $failureMessage;

    public function __construct($failureMessage = "", $userMessage = "") {
        parent::__construct($userMessage);
        $this->failureMessage = $failureMessage;
    }

    /**
     * @return string
     */
    final public function getFailureMessage() {
        return $this->failureMessage;
    }
}
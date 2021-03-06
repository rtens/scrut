<?php
namespace rtens\scrut;

use rtens\scrut\tests\FailureSourceLocator;

class Failure extends \RuntimeException {

    private $failureMessage;
    private $failureSource = "unknown source";

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

    /**
     * @param FailureSourceLocator $locator
     * @return $this
     */
    final public function useSourceLocator(FailureSourceLocator $locator) {
        $this->failureSource = $this->getFailureSourceFromLocator($locator);
        return $this;
    }

    protected function getFailureSourceFromLocator(FailureSourceLocator $locator) {
        return $locator->locateSource($this);
    }

    /**
     * @return string
     */
    final public function getFailureSource() {
        return $this->failureSource;
    }
}
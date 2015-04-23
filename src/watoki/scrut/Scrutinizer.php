<?php
namespace watoki\scrut;

use watoki\scrut\listeners\MultiListener;

class Scrutinizer {

    /** @var MultiListener */
    private $listener;

    /** @var array|TestSuite[] */
    private $suites = [];

    function __construct() {
        $this->listener = new MultiListener();
    }

    public function add(TestSuite $suite) {
        $this->suites[] = $suite;
    }

    public function run() {
        $this->listener->onRunStarted();

        foreach ($this->suites as $suite) {
            $suite->run($this->listener);
        }

        $this->listener->onRunFinished();
    }

    public function listen(ScrutinizeListener $listener) {
        $this->listener->add($listener);
    }
}
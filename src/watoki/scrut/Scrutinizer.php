<?php
namespace watoki\scrut;

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
        foreach ($this->suites as $suite) {
            $suite->run($this->listener);
        }
    }

    public function listen(ScrutinizeListener $listener) {
        $this->listener->add($listener);
    }
}
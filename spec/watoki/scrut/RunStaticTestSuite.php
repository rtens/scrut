<?php
namespace spec\watoki\scrut;

use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\Scrutinizer;
use watoki\scrut\suites\StaticTestSuite;

class RunStaticTestSuite extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    /** @var Scrutinizer */
    private $scrutinizer;

    protected function before() {
        $this->listener = new ArrayListener();
        $this->scrutinizer = new Scrutinizer();
        $this->scrutinizer->listen($this->listener);
    }

    function emptySuite() {
        $this->scrutinizer->add(new RunStaticTestSuite_EmptySuite());
        $this->scrutinizer->run();

        /** @var IncompleteTestResult $result */
        $result = $this->listener->getResult(0);

        $this->assert($this->listener->count(), 1);
        $this->assert($result instanceof IncompleteTestResult);
        $this->assert($result->failure()->getFailureMessage(), 'Empty test suite');
        $this->assert($result->failure()->getFailureFileAndLine(), __FILE__ . ':45');
    }

    function runPublicMethods() {
        $this->markIncomplete();
    }

    function filterMethods() {
        $this->markIncomplete();
    }
}

class RunStaticTestSuite_EmptySuite extends StaticTestSuite {

}
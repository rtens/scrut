<?php
namespace watoki\scrut;

use watoki\factory\Factory;
use watoki\scrut\failures\CaughtErrorFailure;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\tests\generic\GenericTestCase;
use watoki\scrut\tests\statics\StaticTestSuite;

class CatchWarningsAndErrors extends StaticTestSuite {

    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
    }

    function catchWarning() {
        $test = new GenericTestCase(new Factory(), function () {
            /** @noinspection PhpParamsInspection */
            $this->foo();
        }, 'foo');
        $test->run($this->listener);

        $this->assertErrorIsCaught('E_WARNING', "Missing argument 1");
    }

    function catchError() {
        $test = new GenericTestCase(new Factory(), function () {
            $this->bar("foo");
        }, 'foo');
        $test->run($this->listener);

        $this->assertErrorIsCaught('E_RECOVERABLE_ERROR', "must be callable, string given");
    }

    private function assertErrorIsCaught($type, $withMessage) {
        $this->assert->size($this->listener->results, 1);
        $result = $this->listener->results[0];
        if (!($result instanceof FailedTestResult)) {
            $this->fail("Should be a FailedTestResult");
        }
        $this->assert->isInstanceOf($result->getFailure(), CaughtErrorFailure::class);
        $this->assert->contains($result->getFailure()->getFailureMessage(), "Caught " . $type);
        $this->assert->contains($result->getFailure()->getMessage(), $withMessage);
    }

    private function foo($a) {
    }

    private function bar(callable $b) {
    }
}
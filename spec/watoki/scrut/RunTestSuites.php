<?php
namespace spec\watoki\scrut;

require_once __DIR__ . '/../../../bootstrap.php';

use watoki\scrut\failures\CaughtExceptionFailure;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\listeners\ConsoleListener;
use watoki\scrut\suites\DynamicTestSuite;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\Scrutinizer;
use watoki\scrut\suites\StaticTestSuite;

class RunTestSuites extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    /** @var Scrutinizer */
    private $scrutinizer;

    protected function before() {
        $this->listener = new ArrayListener();
        $this->scrutinizer = new Scrutinizer($this->listener);
        $this->scrutinizer->listen($this->listener);
    }

    public function noSuites() {
        $this->scrutinizer->run();
        $this->assert($this->listener->count(), 0);
    }

    public function emptySuite() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo"));
        $this->scrutinizer->run();
        $this->assert($this->listener->count(), 0);
    }

    public function simpleSuite() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
            }
        ]));
        $this->scrutinizer->run();

        $this->assert($this->listener->count(), 1);
        $this->assert($this->listener->hasStarted("Foo::bar"));
        $this->assert($this->listener->hasFinished("Foo::bar"));
    }

    public function secondListener() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
            }
        ]));
        $secondListener = new ArrayListener();
        $this->scrutinizer->listen($secondListener);
        $this->scrutinizer->run();

        $this->assert($this->listener->count(), 1);
        $this->assert($this->listener->hasStarted("Foo::bar"));
        $this->assert($this->listener->hasFinished("Foo::bar"));
    }

    public function passingTest() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
                // Passes
            }
        ]));
        $this->scrutinizer->run();

        $this->assert($this->listener->getResult(0) instanceof PassedTestResult);
        $this->assert($this->listener->getResult("Foo::bar") instanceof PassedTestResult);
    }

    public function failingTest() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
                throw new \Exception('Failed miserably');
            }
        ]));
        $this->scrutinizer->run();

        /** @var FailedTestResult $result */
        $result = $this->listener->getResult("Foo::bar");
        $this->assert($result instanceof FailedTestResult);
        $this->assert($result->failure() instanceof CaughtExceptionFailure);
        $this->assert($result->failure()->getMessage(), "Failed miserably");
    }
}

$s = new Scrutinizer();
$s->listen(new ConsoleListener());
$s->add(new RunTestSuites());
$s->run();
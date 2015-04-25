<?php
namespace spec\watoki\scrut;

use watoki\scrut\Asserter;
use watoki\scrut\Failure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\tests\GenericTestCase;
use watoki\scrut\tests\GenericTestSuite;
use watoki\scrut\tests\PlainTestCase;
use watoki\scrut\tests\PlainTestSuite;
use watoki\scrut\tests\StaticTestCase;
use watoki\scrut\tests\StaticTestSuite;

class FindLocationOfFailure extends StaticTestSuite {

    protected function getTests() {
        return [
            new FindLocationOfFailure_InGenericTestSuite(),
            new FindLocationOfFailure_InStaticTestSuite(),
            new FindLocationOfFailure_InPlainTestSuite(),
        ];
    }
}

class FindLocationOfFailure_InGenericTestSuite extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    /** @var GenericTestSuite */
    private $suite;

    /** @internal */
    public function getName() {
        return 'InGenericTestSuite';
    }

    protected function before() {
        $this->listener = new ArrayListener();
        $this->suite = new GenericTestSuite("Foo");
    }

    function directlyThrownFailure() {
        $this->runGenericTestCase(function () {
            throw new Failure();
        });
        $this->assertLocationIsAtLine(__LINE__ - 2);
    }

    function failedAssertion() {
        $this->runGenericTestCase(function (Asserter $assert) {
            $assert->isTrue(false);
        });
        $this->assertLocationIsAtLine(__LINE__ - 2);
    }

    function incompleteTest() {
        $this->runGenericTestCase(function () {
            throw new IncompleteTestFailure();
        });
        $this->assertLocationIsAtLine(__LINE__ - 2);
    }

    function directlyThrownException() {
        $this->runGenericTestCase(function () {
            throw new \InvalidArgumentException();
        });
        $this->assertLocationIsAtLine(__LINE__ - 2);
    }

    function indirectlyThrownException() {
        $this->runGenericTestCase(function () {
            $this->throwException();
        });
        $this->assertLocationIsAtLine(__LINE__ - 2);
    }

    function emptyTestCase() {
        $this->suite->add(new GenericTestCase('bar', function () {
        }));
        $this->suite->run($this->listener);

        $this->assertLocationIsAtLine(__LINE__ - 3);
    }

    function emptyTestSuite() {
        $suite = new GenericTestSuite("Foo");
        $suite->run($this->listener);

        $this->assertLocationIsAtLine(__LINE__ - 3);
    }

    function caughtError() {
        $this->runGenericTestCase(function () {
            $this->raiseAWarning();
        });
        $this->assertLocationIsAtLine(__LINE__ - 2);
    }

    private function throwException() {
        throw new \InvalidArgumentException();
    }

    private function runGenericTestCase($callback) {
        $this->suite->add(new GenericTestCase('bar', $callback));
        $this->suite->run($this->listener);
    }

    private function assertLocationIsAtLine($line) {
        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->failure()->getLocation(), __FILE__ . '(' . ($line) . ')');
    }

    private function raiseAWarning() {
        /** @noinspection PhpParamsInspection */
        $this->runGenericTestCase();
    }
}

class FindLocationOfFailure_InStaticTestSuite extends StaticTestSuite {

    /** @var StaticTestSuite */
    private $suite;

    /** @var ArrayListener */
    private $listener;

    /** @internal */
    public function getName() {
        return 'InStaticTestSuite';
    }

    protected function before() {
        $this->suite = new FindLocationOfFailure_Foo();
        $this->listener = new ArrayListener();
    }

    function directlyThrownFailure() {
        $this->executeTestCase('throwFailure');
        $this->assertLocationIsAtLine(3);
    }

    function caughtError() {
        $this->executeTestCase('raiseAWarning');
        $this->assertLocationIsAtLine(34);
    }

    function failedAssertion() {
        $this->executeTestCase('failAssertion');
        $this->assertLocationIsAtLine(7);
    }

    function failedAssertInvocation() {
        $this->executeTestCase('failAssertInvocation');
        $this->assertLocationIsAtLine(30);
    }

    function incompleteTest() {
        $this->executeTestCase('incompleteTest');
        $this->assertLocationIsAtLine(11);
    }

    function directlyThrownException() {
        $this->executeTestCase('directlyThrowException');
        $this->assertLocationIsAtLine(15);
    }

    function indirectlyThrownException() {
        $this->executeTestCase('indirectlyThrowException');
        $this->assertLocationIsAtLine(19);
    }

    function indirectAssertion() {
        $this->executeTestCase('indirectAssertion');
        $this->assertLocationIsAtLine(23);
    }

    function emptyTestCase() {
        $this->executeTestCase('noAssertions');
        $this->assertLocationIsAtLine(26);
    }

    function emptyTestSuite() {
        $this->suite = new FindLocationOfFailure_Empty();
        $this->suite->run($this->listener);
        $this->assertLocationIsAtLine(0);
    }

    private function executeTestCase($name) {
        $test = new StaticTestCase(new \ReflectionMethod($this->suite, $name));
        $test->run($this->listener);
    }

    private function assertLocationIsAtLine($line) {
        $start = (new \ReflectionClass($this->suite))->getStartLine();

        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->failure()->getLocation(), __FILE__ . '(' . ($start + $line) . ')');
    }

    public static function throwException() {
        throw new \Exception();
    }

}

class FindLocationOfFailure_InPlainTestSuite extends StaticTestSuite {

    /** @var PlainTestSuite */
    private $suite;

    /** @var ArrayListener */
    private $listener;

    /** @internal */
    public function getName() {
        return 'InPlainTestSuite';
    }

    protected function before() {
        $this->suite = new PlainTestSuite(new FindLocationOfFailure_PlainFoo());
        $this->listener = new ArrayListener();
    }

    function directlyThrownFailure() {
        $this->executeTestCase('throwFailure');
        $this->assertLocationIsAtLine(3);
    }

    function caughtError() {
        $this->executeTestCase('raiseAWarning');
        $this->assertLocationIsAtLine(30);
    }

    function failedAssertion() {
        $this->executeTestCase('failAssertion');
        $this->assertLocationIsAtLine(7);
    }

    function incompleteTest() {
        $this->executeTestCase('incompleteTest');
        $this->assertLocationIsAtLine(11);
    }

    function directlyThrownException() {
        $this->executeTestCase('directlyThrowException');
        $this->assertLocationIsAtLine(15);
    }

    function indirectlyThrownException() {
        $this->executeTestCase('indirectlyThrowException');
        $this->assertLocationIsAtLine(19);
    }

    function indirectAssertion() {
        $this->executeTestCase('indirectAssertion');
        $this->assertLocationIsAtLine(23);
    }

    function emptyTestCase() {
        $this->executeTestCase('noAssertions');
        $this->assertLocationIsAtLine(26);
    }

    function emptyTestSuite() {
        $this->suite = new PlainTestSuite(new FindLocationOfFailure_PlainEmpty());
        $this->suite->run($this->listener);
        $this->assertLocationIsAtLine(0);
    }

    private function executeTestCase($name) {
        $test = new PlainTestCase(new \ReflectionMethod($this->suite->getSuite(), $name));
        $test->run($this->listener);
    }

    private function assertLocationIsAtLine($line) {
        $start = (new \ReflectionClass($this->suite->getSuite()))->getStartLine();

        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->failure()->getLocation(), __FILE__ . '(' . ($start + $line) . ')');
    }

}

class FindLocationOfFailure_Empty extends StaticTestSuite {

}

class FindLocationOfFailure_Foo extends StaticTestSuite {

    function throwFailure() {
        throw new Failure();
    }

    function failAssertion() {
        $this->assert->isTrue(false);
    }

    function incompleteTest() {
        $this->markIncomplete();
    }

    function directlyThrowException() {
        throw new \Exception();
    }

    function indirectlyThrowException() {
        FindLocationOfFailure_InStaticTestSuite::throwException();
    }

    function indirectAssertion() {
        $this->failAssertion();
    }

    function noAssertions() {
    }

    function failAssertInvocation() {
        $this->assert(false);
    }

    function raiseAWarning() {
        $this->beBad();
    }

    private function beBad() {
        /** @noinspection PhpParamsInspection */
        $this->assert();
    }
}

class FindLocationOfFailure_PlainEmpty {
}

class FindLocationOfFailure_PlainFoo {

    function throwFailure() {
        throw new Failure();
    }

    function failAssertion(Asserter $assert) {
        $assert->isTrue(false);
    }

    function incompleteTest() {
        throw new IncompleteTestFailure;
    }

    function directlyThrowException() {
        throw new \Exception();
    }

    function indirectlyThrowException() {
        FindLocationOfFailure_InStaticTestSuite::throwException();
    }

    function indirectAssertion(Asserter $assert) {
        $this->failAssertion($assert);
    }

    function noAssertions() {
    }

    function raiseAWarning() {
        $this->beBad();
    }

    private function beBad() {
        /** @noinspection PhpParamsInspection */
        $this->indirectAssertion();
    }
}
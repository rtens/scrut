<?php
namespace spec\watoki\scrut;

use watoki\scrut\Asserter;
use watoki\scrut\Failure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\tests\FailureSourceLocator;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\tests\generic\GenericTestCase;
use watoki\scrut\tests\generic\GenericTestSuite;
use watoki\scrut\tests\plain\PlainTestCase;
use watoki\scrut\tests\plain\PlainTestSuite;
use watoki\scrut\tests\statics\StaticTestCase;
use watoki\scrut\tests\statics\StaticTestSuite;

class FindLocationOfFailure extends StaticTestSuite {

    protected function getTests() {
        return [
            new FindLocationOfFailure_InGenericTestSuite(),
            new FindLocationOfFailure_InStaticTestSuite(),
            new FindLocationOfFailure_InPlainTestSuite(),
        ];
    }
}

class FindLocationOfFailure_TestSuite extends StaticTestSuite {

    /** @var ArrayListener */
    protected $listener;

    /** @internal */
    public function getName() {
        return substr(get_class($this), -18);
    }

    protected function before() {
        $this->listener = new ArrayListener();
    }

    protected function assertLocationIsAtLine($line) {
        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $expected = FailureSourceLocator::formatFileAndLine(__FILE__, $line);
        $this->assert($this->listener->testResults[0]->getFailureSourceLocator()->locate($result->failure()), $expected);
    }

}

class FindLocationOfFailure_InGenericTestSuite extends FindLocationOfFailure_TestSuite {

    /** @var GenericTestSuite */
    private $suite;

    /** @internal */
    public function getName() {
        return 'InGenericTestSuite';
    }

    protected function before() {
        parent::before();
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

    private function raiseAWarning() {
        /** @noinspection PhpParamsInspection */
        $this->runGenericTestCase();
    }
}

class FindLocationOfFailure_InStaticTestSuite extends FindLocationOfFailure_TestSuite {

    /** @var StaticTestSuite */
    private $suite;

    protected function before() {
        parent::before();
        $this->suite = new FindLocationOfFailure_Foo();
    }

    function directlyThrownFailure() {
        $this->executeTestCase('throwFailure');
        $this->assertLocationIsAtLineOfSuite(3);
    }

    function caughtError() {
        $this->executeTestCase('raiseAWarning');
        $this->assertLocationIsAtLineOfSuite(34);
    }

    function failedAssertion() {
        $this->executeTestCase('failAssertion');
        $this->assertLocationIsAtLineOfSuite(7);
    }

    function failedAssertInvocation() {
        $this->executeTestCase('failAssertInvocation');
        $this->assertLocationIsAtLineOfSuite(30);
    }

    function incompleteTest() {
        $this->executeTestCase('incompleteTest');
        $this->assertLocationIsAtLineOfSuite(11);
    }

    function directlyThrownException() {
        $this->executeTestCase('directlyThrowException');
        $this->assertLocationIsAtLineOfSuite(15);
    }

    function indirectlyThrownException() {
        $this->executeTestCase('indirectlyThrowException');
        $this->assertLocationIsAtLineOfSuite(19);
    }

    function indirectAssertion() {
        $this->executeTestCase('indirectAssertion');
        $this->assertLocationIsAtLineOfSuite(23);
    }

    function emptyTestCase() {
        $this->executeTestCase('noAssertions');
        $this->assertLocationIsAtLineOfSuite(26);
    }

    function emptyTestSuite() {
        $this->suite = new FindLocationOfFailure_Empty();
        $this->suite->run($this->listener);
        $this->assertLocationIsAtLineOfSuite(0);
    }

    private function executeTestCase($name) {
        $test = new StaticTestCase(new \ReflectionMethod($this->suite, $name));
        $test->run($this->listener);
    }

    private function assertLocationIsAtLineOfSuite($line) {
        $start = (new \ReflectionClass($this->suite))->getStartLine();
        $this->assertLocationIsAtLine($start + $line);
    }

    public static function throwException() {
        throw new \Exception();
    }

}

class FindLocationOfFailure_InPlainTestSuite extends FindLocationOfFailure_TestSuite {

    /** @var PlainTestSuite */
    private $suite;

    protected function before() {
        parent::before();
        $this->suite = new PlainTestSuite(new FindLocationOfFailure_PlainFoo());
    }

    function directlyThrownFailure() {
        $this->executeTestCase('throwFailure');
        $this->assertLocationIsAtLineOfSuite(3);
    }

    function caughtError() {
        $this->executeTestCase('raiseAWarning');
        $this->assertLocationIsAtLineOfSuite(30);
    }

    function failedAssertion() {
        $this->executeTestCase('failAssertion');
        $this->assertLocationIsAtLineOfSuite(7);
    }

    function incompleteTest() {
        $this->executeTestCase('incompleteTest');
        $this->assertLocationIsAtLineOfSuite(11);
    }

    function directlyThrownException() {
        $this->executeTestCase('directlyThrowException');
        $this->assertLocationIsAtLineOfSuite(15);
    }

    function indirectlyThrownException() {
        $this->executeTestCase('indirectlyThrowException');
        $this->assertLocationIsAtLineOfSuite(19);
    }

    function indirectAssertion() {
        $this->executeTestCase('indirectAssertion');
        $this->assertLocationIsAtLineOfSuite(23);
    }

    function emptyTestCase() {
        $this->executeTestCase('noAssertions');
        $this->assertLocationIsAtLineOfSuite(26);
    }

    function emptyTestSuite() {
        $this->suite = new PlainTestSuite(new FindLocationOfFailure_PlainEmpty());
        $this->suite->run($this->listener);
        $this->assertLocationIsAtLineOfSuite(0);
    }

    private function executeTestCase($name) {
        $test = new PlainTestCase(new \ReflectionMethod($this->suite->getSuite(), $name));
        $test->run($this->listener);
    }

    private function assertLocationIsAtLineOfSuite($line) {
        $start = (new \ReflectionClass($this->suite->getSuite()))->getStartLine();
        $this->assertLocationIsAtLine($start + $line);
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
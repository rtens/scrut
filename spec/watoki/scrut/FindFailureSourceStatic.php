<?php
namespace spec\watoki\scrut;

use watoki\scrut\Asserter;
use watoki\scrut\Failure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\NotPassedTestResult;
use watoki\scrut\TestName;
use watoki\scrut\tests\FailureSourceLocator;
use watoki\scrut\tests\generic\GenericTestCase;
use watoki\scrut\tests\generic\GenericTestSuite;
use watoki\scrut\tests\plain\PlainTestSuite;
use watoki\scrut\tests\statics\StaticTestCase;
use watoki\scrut\tests\statics\StaticTestSuite;

class FindFailureSource extends StaticTestSuite {

    protected function getTests() {
        return [
            new FindFailureSource_InGenericTestSuite(),
            new FindFailureSource_InStaticTestSuite(),
            new FindFailureSource_InPlainTestSuite(),
        ];
    }
}

class FindFailureSource_TestSuite extends StaticTestSuite {

    /** @var ArrayListener */
    protected $listener;

    protected function before() {
        $this->listener = new ArrayListener();
    }

    public function getName() {
        return (new TestName(FindFailureSource::class))
            ->with(substr(get_class($this), strlen(FindFailureSource::class) + 1, -9));
    }


    protected function assertLocationIsAtLine($line) {
        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, NotPassedTestResult::class);

        $expected = FailureSourceLocator::formatFileAndLine(__FILE__, $line);
        $this->assert($result->getFailure()->getFailureSource(), $expected);
    }

}

class FindFailureSource_InGenericTestSuite extends FindFailureSource_TestSuite {

    /** @var GenericTestSuite */
    private $suite;

    /** @internal */
    protected function getOwnName() {
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
        $this->suite->add(new GenericTestCase(function () {
        }, 'bar'));
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

    function caughtNotice() {
        $this->runGenericTestCase(function () {
            assert(false);
        });
        $this->assertLocationIsAtLine(__LINE__ - 2);
    }

    private function throwException() {
        throw new \InvalidArgumentException();
    }

    private function runGenericTestCase($callback) {
        $this->suite->add(new GenericTestCase($callback, 'bar'));
        $this->suite->run($this->listener);
    }

    private function raiseAWarning() {
        /** @noinspection PhpParamsInspection */
        $this->runGenericTestCase();
    }
}

class FindFailureSource_InStaticTestSuite extends FindFailureSource_TestSuite {

    /** @var StaticTestSuite */
    private $suite;

    protected function before() {
        parent::before();
        $this->suite = new FindFailureSource_StaticFoo();
    }

    function directlyThrownFailure() {
        $this->executeTestCase('throwFailure');
        $this->assertLocationIsAtLineOfSuite(3);
    }

    function caughtError() {
        $this->executeTestCase('raiseAWarning');
        $this->assertLocationIsAtLineOfSuite(34);
    }

    function caughtNotice() {
        $this->executeTestCase('raiseNotice');
        $this->assertLocationIsAtLineOfSuite(38);
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
        $this->suite = new FindFailureSource_Empty();
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

class FindFailureSource_InPlainTestSuite extends FindFailureSource_TestSuite {

    /** @var PlainTestSuite */
    private $testClass;

    protected function before() {
        parent::before();
        $this->testClass = FindFailureSource_PlainFoo::class;
    }

    function directlyThrownFailure() {
        $this->executeTestCase('throwFailure');
        $this->assertLocationIsAtLineOfSuite(3);
    }

    function caughtError() {
        $this->executeTestCase('raiseAWarning');
        $this->assertLocationIsAtLineOfSuite(30);
    }

    function caughtNotice() {
        $this->executeTestCase('raiseNotice');
        $this->assertLocationIsAtLineOfSuite(34);
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
        $this->testClass = FindFailureSource_PlainEmpty::class;
        $suite = new PlainTestSuite($this->testClass);
        $suite->run($this->listener);
        $this->assertLocationIsAtLineOfSuite(0);
    }

    private function executeTestCase($name) {
        $suite = new PlainTestSuite($this->testClass);
        $suite->setMethodFilter(function (\ReflectionMethod $method) use ($name) {
            return $method->getName() == $name;
        });
        $suite->run($this->listener);
    }

    private function assertLocationIsAtLineOfSuite($line) {
        $start = (new \ReflectionClass($this->testClass))->getStartLine();
        $this->assertLocationIsAtLine($start + $line);
    }

}

class FindFailureSource_Empty extends StaticTestSuite {

}

class FindFailureSource_StaticFoo extends StaticTestSuite {

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
        FindFailureSource_InStaticTestSuite::throwException();
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

    function raiseNotice() {
        assert(false);
    }

    private function beBad() {
        /** @noinspection PhpParamsInspection */
        $this->assert();
    }
}

class FindFailureSource_PlainEmpty {
}

class FindFailureSource_PlainFoo {

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
        FindFailureSource_InStaticTestSuite::throwException();
    }

    function indirectAssertion(Asserter $assert) {
        $this->failAssertion($assert);
    }

    function noAssertions(Asserter $asserter) {
    }

    function raiseAWarning() {
        $this->beBad();
    }

    function raiseNotice() {
        assert(false);
    }

    private function beBad() {
        /** @noinspection PhpParamsInspection */
        $this->indirectAssertion();
    }
}
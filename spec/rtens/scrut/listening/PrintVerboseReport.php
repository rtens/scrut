<?php
namespace spec\rtens\scrut\listening;

use rtens\scrut\Asserter;
use rtens\scrut\listeners\VerboseConsoleListener;
use rtens\scrut\tests\generic\GenericTestSuite;

class PrintVerboseReport {

    private $output = '';

    /** @var GenericTestSuite */
    private $suite;

    function before() {
        $this->suite = new GenericTestSuite('Foo');
    }

    function passingTest(Asserter $assert) {
        $this->suite->test('one', function (Asserter $assert) {
            $assert->pass();
        });

        $this->runAndAssertOutput($assert, [
            'Foo',
            'Foo::one -> Passed',
            '',
            '=D 1 Passed'
        ]);
    }

    function failingTest(Asserter $assert) {
        $this->suite->test('one', function (Asserter $assert) {
            $assert->fail('Oh no!');
        });
        $this->runAndAssertOutput($assert, [
            'Foo',
            'Foo::one -> Failed',
            '',
            '---- Failed ----',
            'Foo::one [FILE:-12]',
            '    Failed',
            '    Oh no!',
            '',
            '=( 1 Failed'
        ]);
    }

    function incompleteTest(Asserter $assert) {
        $this->suite->test('one', function (Asserter $assert) {
            $assert->incomplete('Not done');
        });
        $this->runAndAssertOutput($assert, [
            'Foo',
            'Foo::one -> Incomplete',
            '',
            '---- Incomplete ----',
            'Foo::one [FILE:-11]',
            '    Not done',
            '',
            '=| 1 Incomplete'
        ]);
    }

    function emptySuite(Asserter $assert) {
        $this->suite->suite('empty');

        $this->runAndAssertOutput($assert, [
            'Foo',
            'Foo::empty -> Incomplete',
            '',
            '---- Incomplete ----',
            'Foo::empty [FILE:-11]',
            '    Empty test suite',
            '',
            '=| 1 Incomplete'
        ]);
    }

    private function runAndAssertOutput(Asserter $assert, $expected) {
        $listener = new VerboseConsoleListener(function ($text) {
            $this->output .= $text;
        });
        $this->suite->run($listener);

        $this->assertOutput($assert, $expected, new \Exception());
    }

    private function assertOutput(Asserter $assert, $expected, \Exception $exception = null) {
        $exception = $exception ?: new \Exception();
        $trace = $exception->getTrace();

        $expected = preg_replace_callback('/FILE:(-?\d+)/', function ($matches) use ($trace) {
            return $trace[0]['file'] . ':' . ($trace[0]['line'] + intval($matches[1]));
        }, $expected);

        $assert(explode("\n", trim($this->output)), $expected);
    }
}
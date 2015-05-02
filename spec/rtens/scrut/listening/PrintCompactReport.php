<?php
namespace spec\rtens\scrut\listening;

use rtens\scrut\Assert;
use rtens\scrut\Failure;
use rtens\scrut\listeners\CompactConsoleListener;
use rtens\scrut\results\NotPassedTestResult;
use rtens\scrut\TestName;

class PrintCompactReport extends ListeningSpecification {

    function passingTest(Assert $assert) {
        $this->suite->test('one', function (Assert $assert) {
            $assert->pass();
        });

        $this->runAndAssertOutput($assert, [
            '.',
            '',
            '=D 1 Passed'
        ]);
    }

    function failingTest(Assert $assert) {
        $this->suite->test('one', function (Assert $assert) {
            $assert->fail('Oh no!');
        });
        $this->runAndAssertOutput($assert, [
            'F',
            '',
            '---- Failed ----',
            'Foo::one [FILE:-11]',
            '    Failed',
            '    Oh no!',
            '',
            '=( 1 Failed'
        ]);
    }

    function incompleteTest(Assert $assert) {
        $this->suite->test('one', function (Assert $assert) {
            $assert->incomplete('Not done');
        });
        $this->runAndAssertOutput($assert, [
            'I',
            '',
            '---- Incomplete ----',
            'Foo::one [FILE:-10]',
            '    Not done',
            '',
            '=| 1 Incomplete'
        ]);
    }

    function mixedResults(Assert $assert) {
        $this->suite->test('one', function () {
        });
        $this->suite->test('two', function (Assert $assert) {
            $assert->pass();
        });
        $this->suite->test('three', function (Assert $assert) {
            $assert->fail('Miserably');
        });
        $this->suite->test('four', function (Assert $assert) {
            $assert->pass();
        });
        $this->suite->test('five', function () {
        });

        $this->runAndAssertOutput($assert, [
            'I.F.I',
            '',
            '---- Incomplete ----',
            'Foo::one [FILE:-28]',
            '    No assertions made',
            'Foo::five [FILE:-17]',
            '    No assertions made',
            '',
            '---- Failed ----',
            'Foo::three [FILE:-23]',
            '    Failed',
            '    Miserably',
            '',
            '=( 2 Passed, 2 Incomplete, 1 Failed'
        ]);
    }

    function unknownResult(Assert $assert) {
        $test = new TestName('Foo', 'one');
        $listener = new CompactConsoleListener(function ($text) {
            $this->output .= $text;
        });

        $listener->onStarted($test);
        $listener->onResult($test, new NotPassedTestResult(new Failure()));
        $listener->onFinished($test);

        $this->assertOutput($assert, ['?', '', 'Unknown result']);
    }

    protected function createListener(callable $printer) {
        return new CompactConsoleListener($printer);
    }
}
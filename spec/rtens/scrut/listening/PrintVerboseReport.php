<?php
namespace spec\rtens\scrut\listening;

use rtens\scrut\Asserter;
use rtens\scrut\listeners\VerboseConsoleListener;

class PrintVerboseReport extends ListeningSpecification {

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

    protected function createListener(callable $printer) {
        return new VerboseConsoleListener($printer);
    }
}
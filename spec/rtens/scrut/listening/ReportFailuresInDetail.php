<?php
namespace spec\rtens\scrut\listening;

use rtens\scrut\Asserter;
use rtens\scrut\listeners\FailConsoleListener;

class ReportFailuresInDetail extends ListeningSpecification {

    function passing(Asserter $assert) {
        $this->suite->test('one', function (Asserter $assert) {
            $assert->pass();
        });
        $this->runAndAssertOutput($assert, [
            'Passed =D'
        ]);
    }

    function failing(Asserter $assert) {
        $this->suite->test('one', function (Asserter $assert) {
            $assert->fail();
        });
        $this->runTestSuite();

        $assert(end($this->outputLines), 'FAILED =(');
    }

    function mixedResults(Asserter $assert) {
        $this->suite->test('one', function (Asserter $assert) {
            $assert->pass();
        });
        $this->suite->test('two', function (Asserter $assert) {
            $assert->fail();
        });
        $this->suite->test('three', function (Asserter $assert) {
            $assert->incomplete();
        });
        $this->runTestSuite();

        $assert(end($this->outputLines), 'FAILED =(');
    }

    function detailedFailureDescription(Asserter $assert) {
        $this->suite->test('one', function (Asserter $assert) {
            $assert->fail('Oh no!');
        });
        $this->suite->test('two', function (Asserter $assert) {
            $assert->fail('Not this one');
        });
        $this->runAndAssertOutput($assert, [
            'FAILED: Foo::one',
            '   Source:',
            '      FILE:-25',
            '   Code:',
            '      $assert->fail(\'Oh no!\');',
            '   Message:',
            '      Failed',
            '      Oh no!',
            '',
            'FAILED: Foo::two',
            '   Source:',
            '      FILE:-22',
            '   Code:',
            '      $assert->fail(\'Not this one\');',
            '   Message:',
            '      Failed',
            '      Not this one',
            '',
            'FAILED =('
        ]);
    }

    /**
     * @param callable $printer
     * @return \rtens\scrut\listeners\ConsoleListener
     */
    protected function createListener(callable $printer) {
        return new FailConsoleListener($printer);
    }
}
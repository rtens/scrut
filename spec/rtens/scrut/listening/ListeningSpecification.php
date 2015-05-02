<?php
namespace spec\rtens\scrut\listening;

use rtens\scrut\Asserter;
use rtens\scrut\tests\generic\GenericTestSuite;

abstract class ListeningSpecification {

    protected $output = '';
    protected $outputLines;

    /** @var \rtens\scrut\listeners\ConsoleListener */
    protected $listener;

    /** @var GenericTestSuite */
    protected $suite;

    function before() {
        $this->suite = new GenericTestSuite('Foo');
        $this->listener = $this->createListener(function ($text) {
            $this->output .= $text;
        });
    }

    protected function runAndAssertOutput(Asserter $assert, $expected) {
        $this->runTestSuite();
        $this->assertOutput($assert, $expected, new \Exception());
    }

    protected function runTestSuite() {
        $this->suite->run($this->listener);
        $this->outputLines = explode(PHP_EOL, trim($this->output));
    }

    protected function assertOutput(Asserter $assert, $expected, \Exception $exception = null) {
        $exception = $exception ?: new \Exception();
        $trace = $exception->getTrace();

        $expected = preg_replace_callback('/FILE:(-?\d+)/', function ($matches) use ($trace) {
            return $trace[0]['file'] . ':' . ($trace[0]['line'] + intval($matches[1]));
        }, $expected);

        $this->outputLines = explode(PHP_EOL, trim($this->output));
        $assert($this->outputLines, $expected);
    }

    /**
     * @param callable $printer
     * @return \rtens\scrut\listeners\ConsoleListener
     */
    abstract protected function createListener(callable $printer);
}
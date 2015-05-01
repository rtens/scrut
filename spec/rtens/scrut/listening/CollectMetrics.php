<?php
namespace spec\rtens\scrut\listening;

use rtens\scrut\Asserter;
use rtens\scrut\listeners\MetricConsoleListener;
use rtens\scrut\tests\generic\GenericTestSuite;

class CollectMetrics {

    /** @var GenericTestSuite */
    private $suite;

    /** @var CollectMetrics_Listener */
    private $listener;

    function before() {
        $this->listener = new CollectMetrics_Listener(1000);
        $this->suite = new GenericTestSuite('Foo');
    }

    function showTotal(Asserter $assert) {
        $this->test('one', 1);
        $this->outputShouldBe($assert, ['Total foo: 1.0']);
    }

    function transformLargeValues(Asserter $assert) {
        $this->test('A', 159);
        $this->outputShouldBe($assert, ['Total foo: 1.6 +2']);
    }

    function transformSmallValues(Asserter $assert) {
        $this->test('A', 0.02345);
        $this->outputShouldBe($assert, ['Total foo: 2.3 -2']);
    }

    function showTopList(Asserter $assert) {
        $this->test('A', 100);
        $this->test('B', 600);
        $this->test('C', 500);
        $this->test('D', 50);
        $this->test('E', 30);
        $this->test('F', 20);

        $this->outputShouldBe($assert, [
            'Total foo: 1.2 +3',
            '',
            'Top tests:',
            '  6.0 +2 Foo::B',
            '  5.0 +2 Foo::C',
            '  1.0 +2 Foo::A',
            '  5.0 +1 Foo::D',
            '  3.0 +1 Foo::E'
        ]);
    }

    private function test($name, $value) {
        $this->suite->test($name, function () use ($value) {
            $this->listener->currentValue += $value;
        });
    }

    private function outputShouldBe(Asserter $assert, $lines) {
        $this->suite->run($this->listener);
        $assert($this->listener->lines, $lines);
    }
}

class CollectMetrics_Listener extends MetricConsoleListener {

    public $lines;
    public $currentValue = 0;
    private $printed;
    private $threshold;

    /**
     * @param float $threshold
     */
    function __construct($threshold) {
        parent::__construct(function ($text) {
            $this->printed .= $text;
            $this->lines = explode(PHP_EOL, trim($this->printed));
        });
        $this->threshold = $threshold;
    }

    /**
     * @return float Minimum total value to show the top values
     */
    protected function getThreshold() {
        return $this->threshold;
    }

    /**
     * @return float
     */
    protected function getCurrentValue() {
        return $this->currentValue;
    }

    /**
     * @return string
     */
    protected function getMetricName() {
        return "foo";
    }

    /**
     * @return array|float[] indexed by the unit symbol
     */
    protected function getUnits() {
        return [
            '+3' => 1000,
            '+2' => 100,
            '+1' => 10,
            '' => 1,
            '-1' => 0.1,
            '-2' => 0.01
        ];
    }
}
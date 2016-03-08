<?php
namespace spec\rtens\scrut\listening;

use rtens\scrut\Assert;
use rtens\scrut\listeners\MetricConsoleListener;

class CollectMetrics extends ListeningSpecification {

    /** @var CollectMetrics_Listener */
    protected $listener;

    protected function createListener(callable $printer) {
        return new CollectMetrics_Listener($printer, 1000);
    }

    function showTotal(Assert $assert) {
        $this->test('one', 1);
        $this->runAndAssertOutput($assert, [
            'Total foo: 1.0'
        ]);
    }

    function transformLargeValues(Assert $assert) {
        $this->test('A', 159);
        $this->runAndAssertOutput($assert, [
            'Total foo: 1.6 +2'
        ]);
    }

    function transformSmallValues(Assert $assert) {
        $this->test('A', 0.02345);
        $this->runAndAssertOutput($assert, [
            'Total foo: 2.3 -2'
        ]);
    }

    function showTopList(Assert $assert) {
        $this->test('A', 100);
        $this->test('B', 600);
        $this->test('C', 500);
        $this->test('D', 50);
        $this->test('E', 30);
        $this->test('F', 20);

        $this->runAndAssertOutput($assert, [
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
}

class CollectMetrics_Listener extends MetricConsoleListener {

    public $lines;
    public $currentValue = 0;
    private $threshold;

    function __construct(callable $printer, $threshold) {
        parent::__construct($printer);
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
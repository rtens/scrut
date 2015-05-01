<?php
namespace watoki\scrut\listeners;

use watoki\scrut\TestName;
use watoki\scrut\TestResult;

abstract class MetricConsoleListener extends ConsoleListener {

    /** @var array|bool */
    private $results = [];

    /** @var null|float */
    private $startValue;

    /** @var array|float[] */
    private $started = [];

    /** @var array|float[] */
    private $values;

    /**
     * @return float Minimum total value to show the top values
     */
    abstract protected function getThreshold();

    /**
     * @return float
     */
    abstract protected function getCurrentValue();

    /**
     * @return string
     */
    abstract protected function getMetricName();

    /**
     * @return array|float[] indexed by the unit symbol
     */
    abstract protected function getUnits();

    public function onStarted(TestName $test) {
        parent::onStarted($test);

        $currentValue = $this->getCurrentValue();

        if (!$this->startValue) {
            $this->startValue = $currentValue;
        }

        $this->started[$test->toString()] = $currentValue;
    }

    public function onResult(TestName $test, TestResult $result) {
        parent::onResult($test, $result);

        $this->results[$test->toString()] = true;
    }

    public function onFinished(TestName $test) {
        parent::onFinished($test);

        if (array_key_exists($test->toString(), $this->results)) {
            $this->values[$test->toString()] = $this->getCurrentValue() - $this->started[$test->toString()];
        }

        unset($this->started[$test->toString()]);
    }

    protected function onEnd() {
        parent::onEnd();

        $total = $this->getCurrentValue() - $this->startValue;

        $this->printLine();
        $this->printLine("Total " . $this->getMetricName() . ": " . $this->format($total));

        if ($total > $this->getThreshold()) {
            $this->printLine();
            $this->printLine("Top tests:");
            arsort($this->values);
            foreach (array_slice($this->values, 0, 5) as $test => $time) {
                $this->printLine($this->format($time, 5) . ' ' . $test);
            }
        }
    }
    private function format($memory, $width = 0) {
        foreach ($this->getUnits() as $unit => $size) {
            if ($memory > $size) {
                return sprintf('%' . $width. '.0f ' . $unit, $memory / $size);
            }
        }
        return 0;
    }

}
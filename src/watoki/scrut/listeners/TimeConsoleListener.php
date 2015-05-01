<?php
namespace watoki\scrut\listeners;

class TimeConsoleListener extends MetricConsoleListener {

    /**
     * @return float Minimum total value to show the top values
     */
    protected function getThreshold() {
        return 3.0;
    }

    /**
     * @return float
     */
    protected function getCurrentValue() {
        return microtime(true);
    }

    /**
     * @return string
     */
    protected function getMetricName() {
        return "time";
    }

    /**
     * @return array|float[] indexed by the unit symbol
     */
    protected function getUnits() {
        return [
            's ' => 1,
            'ms' => 0.001
        ];
    }
}
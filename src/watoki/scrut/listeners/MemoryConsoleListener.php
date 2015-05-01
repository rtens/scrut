<?php
namespace watoki\scrut\listeners;

class MemoryConsoleListener extends MetricConsoleListener {

    /**
     * @return float Minimum total value to show the top values
     */
    protected function getThreshold() {
        return 10 * 1024 * 1024;
    }

    /**
     * @return float
     */
    protected function getCurrentValue() {
        return memory_get_usage(true);
    }

    /**
     * @return string
     */
    protected function getMetricName() {
        return "memory";
    }

    protected function getUnits() {
        return [
            'MiB' => 1024 * 1024,
            'KiB' => 1024,
            'B  ' => 0,
        ];
    }
}
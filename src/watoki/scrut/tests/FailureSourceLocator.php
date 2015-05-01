<?php
namespace watoki\scrut\tests;

abstract class FailureSourceLocator {

    /**
     * @param array $trace
     * @return string
     */
    abstract protected function getExceptionSourceFromTrace($trace);

    /**
     * @return string
     */
    abstract public function locateEmptyTestFailureSource();

    /**
     * @param \Exception $exception
     * @return string
     */
    public function locateSource(\Exception $exception) {
        $first = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'class' => null
        ];

        return $this->getExceptionSourceFromTrace(array_merge([$first], $exception->getTrace()));
    }

    /**
     * @return string
     */
    protected function unknownSource() {
        return "unknown source";
    }

    /**
     * @param array $step with 'file' and 'line'
     * @return string
     */
    protected function formatStep($step) {
        if (!isset($step['file']) || !isset($step['line'])) {
            return $this->unknownSource();
        }
        return self::formatFileAndLine($step['file'], $step['line']);
    }

    /**
     * @param string $file
     * @param int $line
     * @return string
     */
    public static function formatFileAndLine($file, $line) {
        return $file . ':' . $line;
    }
}
<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;

class CaughtExceptionFailure extends Failure {

    public function __construct(\Exception $exception) {
        $location = $this->formatFileAndLine($exception->getFile(), $exception->getLine());
        parent::__construct("Caught [" . get_class($exception) . "] thrown at [" . $location . "]",
            $exception->getMessage(),
            $this->findLocation($exception));
    }
}
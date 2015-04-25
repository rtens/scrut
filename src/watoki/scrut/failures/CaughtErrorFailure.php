<?php
namespace watoki\scrut\failures;


use watoki\scrut\Failure;

class CaughtErrorFailure extends Failure {

    private $errorCode;
    private $errorFile;
    private $errorLine;

    /**
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     */
    public function __construct($message, $code, $file, $line) {
        parent::__construct($message);

        $this->errorCode = $code;
        $this->errorFile = $file;
        $this->errorLine = $line;
    }

    public function getLocation() {
        return $this->formatFileAndLine($this->errorFile, $this->errorLine);
    }

    public function getFailureMessage() {
        return "Caught " . $this->errorType($this->errorCode);
    }

    private function errorType($code) {
        $types = [
            'E_ERROR',
            'E_WARNING',
            'E_PARSE',
            'E_NOTICE',
            'E_CORE_ERROR',
            'E_CORE_WARNING',
            'E_COMPILE_ERROR',
            'E_COMPILE_WARNING',
            'E_USER_ERROR',
            'E_USER_WARNING',
            'E_USER_NOTICE',
            'E_STRICT',
            'E_RECOVERABLE_ERROR',
            'E_DEPRECATED',
            'E_USER_DEPRECATED'
        ];
        foreach ($types as $type) {
            if ($code == constant($type)) {
                return $type;
            }
        }
        return "";
    }

} 
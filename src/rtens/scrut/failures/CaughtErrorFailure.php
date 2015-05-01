<?php
namespace rtens\scrut\failures;


use rtens\scrut\Failure;

class CaughtErrorFailure extends Failure {

    /**
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     */
    public function __construct($message, $code, $file, $line) {
        parent::__construct("Caught " . $this->errorType($code) . " from " . $file . ':' . $line, $message);
    }

    private function errorType($code) {
        $coreConstants = get_defined_constants(true)['Core'];

        foreach ($coreConstants as $type => $constant) {
            if ($code == $constant) {
                return $type;
            }
        }

        return "";
    }

} 
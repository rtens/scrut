<?php
namespace watoki\scrut\failures;


use watoki\scrut\Failure;

class CaughtErrorFailure extends Failure {

    private $errorCode;

    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code) {
        parent::__construct($message);

        $this->errorCode = $code;
    }

    public function getFailureMessage() {
        return "Caught " . $this->errorType($this->errorCode);
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
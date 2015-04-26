<?php
namespace watoki\scrut\failures;


use watoki\scrut\Failure;

class CaughtErrorFailure extends Failure {

    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code) {
        parent::__construct("Caught " . $this->errorType($code), $message);
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
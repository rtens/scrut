<?php
namespace watoki\scrut\assertions;

use watoki\scrut\Assertion;

class ContainsAssertion implements Assertion {

    private $haystack;
    private $needle;

    function __construct($haystack, $needle) {
        $this->haystack = $haystack;
        $this->needle = $needle;
    }

    /**
     * @return string
     */
    public function describeFailure() {
        return "Could not find [" . var_export($this->needle, true) . "]" .
        " in [" . var_export($this->haystack, true) . ']';
    }

    /**
     * @return bool
     */
    public function checksOut() {
        if (is_array($this->haystack)) {
            return in_array($this->needle, $this->haystack);
        } else if (is_string($this->haystack)) {
            return strpos($this->haystack, $this->needle) !== false;
        }

        foreach ($this->haystack as $item) {
            if ($item == $this->needle) {
                return true;
            }
        }

        return false;
    }
}
<?php
namespace rtens\scrut\assertions;

class ContainsAssertion extends ValueAssertion {

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
        return $this->export($this->haystack) . " should contain " . $this->export($this->needle);
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
        if (is_object($this->haystack) || $this->haystack instanceof \Traversable) {
            foreach ($this->haystack as $item) {
                if ($item == $this->needle) {
                    return true;
                }
            }
        }


        return false;
    }
}
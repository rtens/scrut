<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;

class GenericTestCase extends TestCase {

    /** @var string */
    private $name;

    /** @var callable */
    private $callback;

    /** @var \Exception */
    private $creation;

    /**
     * @param string $name
     * @param callable $callback
     */
    function __construct($name, callable $callback) {
        $this->callback = $callback;
        $this->name = $name;
        $this->creation = new \Exception();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    protected function execute(Asserter $assert) {
        call_user_func($this->callback, $assert);
    }

    protected function getNoAssertionsFailureSource() {
        $creation = $this->creation->getTrace()[0];
        return $this->formatFileAndLine($creation['file'], $creation['line']);
    }

    protected function getExceptionSourceFromTrace($trace) {
        foreach ($trace as $i => $step) {
            if (!isset($step['file'])) {
                return $this->formatStep($trace[$i - 1]);
            } else if ($step['class'] == StaticTestSuite::class && $step['function'] == 'execute') {
                return $this->formatStep($trace[$i - 2]);
            } else if ($step['class'] == GenericTestCase::class && $step['function'] == 'execute') {
                return $this->formatStep($trace[$i - 2]);
            }
        }

        return 'unknown location';
    }
}
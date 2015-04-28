<?php
namespace watoki\scrut\tests\generic;

use watoki\scrut\Asserter;
use watoki\scrut\tests\TestCase;

class GenericTestCase extends TestCase {

    /** @var \Exception */
    private $creation;

    /** @var string */
    private $name;

    /** @var callable */
    private $callback;

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

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new GenericFailureSourceLocator($this->creation);
    }
}
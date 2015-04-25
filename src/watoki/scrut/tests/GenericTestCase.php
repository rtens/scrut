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

    /**
     * @return \Exception
     */
    public function getCreation() {
        return $this->creation;
    }

    protected function execute(Asserter $assert) {
        call_user_func($this->callback, $assert);
    }
}
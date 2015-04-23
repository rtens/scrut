<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;

class GenericTestCase extends TestCase {

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
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    protected function execute(Asserter $assert) {
        $callback = $this->callback;
        $callback($assert);
    }
}
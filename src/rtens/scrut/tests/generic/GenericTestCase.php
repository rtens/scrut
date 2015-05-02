<?php
namespace rtens\scrut\tests\generic;

use rtens\scrut\Asserter;
use rtens\scrut\TestName;
use rtens\scrut\tests\TestCase;

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
     * @param null|TestName $parent
     * @param \Exception $creation Used for determining source location
     */
    function __construct($name, callable $callback, TestName $parent = null, \Exception $creation = null) {
        parent::__construct($parent);
        $this->callback = $callback;
        $this->name = $name;
        $this->creation = $creation ?: new \Exception();
    }

    /**
     * @return TestName
     */
    public function getName() {
        return parent::getName()->with($this->name);
    }

    protected function execute(Asserter $assert) {
        call_user_func($this->callback, $assert);
    }

    /**
     * @return \rtens\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new GenericFailureSourceLocator($this->creation);
    }
}
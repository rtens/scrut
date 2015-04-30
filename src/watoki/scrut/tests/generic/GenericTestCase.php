<?php
namespace watoki\scrut\tests\generic;

use watoki\factory\Factory;
use watoki\scrut\Asserter;
use watoki\scrut\TestName;
use watoki\scrut\tests\TestCase;

class GenericTestCase extends TestCase {

    /** @var \Exception */
    private $creation;

    /** @var string */
    private $name;

    /** @var callable */
    private $callback;

    /**
     * @param Factory $factory <-
     * @param callable $callback
     * @param string $name
     * @param null|TestName $parent
     */
    function __construct(Factory $factory, callable $callback, $name, TestName $parent = null) {
        parent::__construct($factory, $parent);
        $this->callback = $callback;
        $this->name = $name;
        $this->creation = new \Exception();
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
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new GenericFailureSourceLocator($this->creation);
    }
}
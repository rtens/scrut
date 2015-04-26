<?php
namespace watoki\scrut\fixtures;

use watoki\scrut\Fixture;

class ExceptionFixture extends Fixture {

    /** @var null|\Exception */
    private $caught;

    public function tryTo($callable) {
        try {
            $callable();
        } catch (\Exception $e) {
            $this->caught = $e;
        }
    }

    public function thenNoExceptionShouldBeThrown() {
        $this->assert->isNull($this->caught, 'An Exception was thrown: ' . get_class($this->caught));
    }

    public function thenAnExceptionShouldBeThrown() {
        $this->assert->not()->isNull($this->caught, 'No Exception was thrown');
    }

    public function thenTheException_ShouldBeThrown($message) {
        $this->thenAnExceptionShouldBeThrown();
        $this->assert->equals($this->caught->getMessage(), $message);
    }

    public function thenAnExceptionContaining_ShouldBeThrown($messagePart) {
        $this->thenAnExceptionShouldBeThrown();
        $this->assert->contains($this->caught->getMessage(), $messagePart);
    }

    public function thenA_ShouldBeThrown($class) {
        $this->thenAnExceptionShouldBeThrown();
        $this->assert->isInstanceOf($this->caught, $class);
    }

    public function getCaughtException() {
        return $this->caught;
    }
}
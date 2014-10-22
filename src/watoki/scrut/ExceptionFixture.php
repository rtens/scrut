<?php
namespace watoki\scrut;

class ExceptionFixture extends Fixture {

    /** @var null|\Exception */
    private $caught;

    public function tryTo($callable, $arguments = array()) {
        try {
            call_user_func_array($callable, $arguments);
        } catch (\Exception $e) {
            $this->caught = $e;
        }
    }

    public function thenTheException_ShouldBeThrown($message) {
        $this->spec->assertNotNull($this->caught, 'No Exception was thrown');
        $this->spec->assertEquals($message, $this->caught->getMessage());
    }

    public function thenNoExceptionShouldBeThrown() {
        $this->spec->assertNull($this->caught, 'An Exception was thrown: ' . get_class($this->caught));
    }

    public function thenA_ShouldBeThrown($class) {
        $this->spec->assertInstanceOf($class, $this->caught);
    }

    public function getCaughtException() {
        return $this->caught;
    }

} 
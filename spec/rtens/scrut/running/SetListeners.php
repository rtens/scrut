<?php
namespace spec\rtens\scrut\running;

use rtens\scrut\Assert;
use rtens\scrut\fixtures\ExceptionFixture;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\listeners\TimeConsoleListener;
use rtens\scrut\running\ScrutCommand;
use watoki\factory\Factory;

/**
 * @property Assert assert <-
 * @property ExceptionFixture try <-
 */
class SetListeners {
    /** @var ArrayListener */
    private $listener;

    /** @var Factory */
    private $factory;

    function before() {
        $this->listener = new ArrayListener();
        $this->factory = new Factory();
    }

    function withFullClassName() {
        $this->factory->setSingleton(ArrayListener::class, $this->listener);
        $this->whenIExecuteTheCommandWithTheArguments(['-l' . ArrayListener::class]);
        $this->thenTheListenerShouldHaveReceivedSomething();
    }

    function withRegisteredListener() {
        $this->factory->setSingleton(TimeConsoleListener::class, $this->listener);
        $this->whenIExecuteTheCommandWithTheArguments(['-lTime']);
        $this->thenTheListenerShouldHaveReceivedSomething();
    }

    function nonExistingListener() {
        $this->try->tryTo(function () {
            $this->whenIExecuteTheCommandWithTheArguments(['-lNot']);
        });
        $this->try->thenTheException_ShouldBeThrown('Could not find listener [Not]');
    }

    private function whenIExecuteTheCommandWithTheArguments($arguments) {
        $command = new ScrutCommand($this->factory);
        $command->execute('foo', $arguments);
    }

    private function thenTheListenerShouldHaveReceivedSomething() {
        $this->assert->size($this->listener->started, 1);
    }
}
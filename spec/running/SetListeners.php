<?php
namespace spec\rtens\scrut\running;

use rtens\scrut\Assert;
use rtens\scrut\fixtures\ExceptionFixture;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\listeners\TimeConsoleListener;
use rtens\scrut\running\ConfigurationReader;
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
        $this->factory->setSingleton($this->listener);
        $this->whenIExecuteTheCommandWithTheArguments(['-l' . ArrayListener::class]);
        $this->thenTheListenerShouldHaveReceivedSomething();
    }

    function withRegisteredListener() {
        $this->factory->setSingleton($this->listener, TimeConsoleListener::class);
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
        $command = new ScrutCommand(new ConfigurationReader('cwd', $this->factory));
        $command->execute($arguments);
    }

    private function thenTheListenerShouldHaveReceivedSomething() {
        $this->assert->size($this->listener->started, 1);
    }
}
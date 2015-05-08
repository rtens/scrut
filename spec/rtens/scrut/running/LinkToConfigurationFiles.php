<?php
namespace rtens\scrut\running;

use rtens\scrut\Assert;
use rtens\scrut\fixtures\ExceptionFixture;
use rtens\scrut\fixtures\FilesFixture;
use rtens\scrut\listeners\ArrayListener;
use watoki\factory\Factory;

/**
 * It is possible to link to other configuration files when defining test suites.
 *
 * @property Assert assert <-
 * @property FilesFixture files <-
 * @property ExceptionFixture try <-
 */
class LinkToConfigurationFiles {

    function nonExistingFile() {
        $this->givenTheConfiguration('scrut.json', [
            'suite' => [
                'name' => 'Foo',
                'suites' => [
                    'other.json'
                ]
            ]
        ]);
        $this->try->tryTo(function () {
            $this->whenIExecuteTheCommand();
        });
        $this->try->thenTheException_ShouldBeThrown('Configuration file [other.json] does no exist.');
    }

    function linkToOtherFiles() {
        $this->givenTheConfiguration('scrut.json', [
            'suite' => [
                'name' => 'Foo',
                'suites' => [
                    'other.json',
                    'bar.json'
                ]
            ]
        ]);
        $this->givenTheConfiguration('other.json', [
            'suite' => [
                'name' => 'Other'
            ]
        ]);
        $this->givenTheConfiguration('bar.json', [
            'suite' => [
                'name' => 'Bar'
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->assert->size($this->listener->started, 3);
        $this->assert->equals($this->listener->started[0]->toString(), 'Foo');
        $this->assert->equals($this->listener->started[1]->toString(), 'Foo::Other');
        $this->assert->equals($this->listener->started[2]->toString(), 'Foo::Bar');
    }

    function linkRecursively() {
        $this->givenTheConfiguration('scrut.json', [
            'suite' => [
                'name' => 'Foo',
                'suites' => [
                    'other.json',
                ]
            ]
        ]);
        $this->givenTheConfiguration('other.json', [
            'suite' => [
                'name' => 'Other',
                'suites' => [
                    'bar.json'
                ]
            ]
        ]);
        $this->givenTheConfiguration('bar.json', [
            'suite' => [
                'name' => 'Bar'
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->assert->size($this->listener->started, 3);
        $this->assert->equals($this->listener->started[0]->toString(), 'Foo');
        $this->assert->equals($this->listener->started[1]->toString(), 'Foo::Other');
        $this->assert->equals($this->listener->started[2]->toString(), 'Foo::Other::Bar');
    }

    function detectLoops() {
        $this->givenTheConfiguration('scrut.json', [
            'suite' => [
                'name' => 'Foo',
                'suites' => [
                    'other.json',
                ]
            ]
        ]);
        $this->givenTheConfiguration('other.json', [
            'suite' => [
                'name' => 'Other',
                'suites' => [
                    'bar.json'
                ]
            ]
        ]);
        $this->givenTheConfiguration('bar.json', [
            'suite' => [
                'name' => 'Bar',
                'suites' => [
                    'scrut.json'
                ]
            ]
        ]);
        $this->try->tryTo(function () {
            $this->whenIExecuteTheCommand();
        });
        $this->try->thenTheException_ShouldBeThrown('Configuration file loop detected while linking to [other.json]');
    }

    /** @var ArrayListener */
    private $listener;

    private function givenTheConfiguration($file, array $config) {
        $this->files->givenTheFile_Containing($file, json_encode($config));
    }

    private function whenIExecuteTheCommand() {
        $this->listener = new ArrayListener();

        $factory = new Factory();
        $factory->setSingleton(ArrayListener::class, $this->listener);

        $command = new ScrutCommand(new ConfigurationReader($this->files->fullPath(), $factory));
        $command->execute(['-c' . json_encode(['listen' => [ArrayListener::class]])]);
    }
}
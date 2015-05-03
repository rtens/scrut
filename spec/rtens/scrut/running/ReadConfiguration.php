<?php
namespace spec\rtens\scrut\running;

use rtens\scrut\Assert;
use rtens\scrut\fixtures\ExceptionFixture;
use rtens\scrut\fixtures\FilesFixture;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\listeners\ResultListener;
use rtens\scrut\running\ConfigurationReader;
use rtens\scrut\running\ScrutCommand;
use rtens\scrut\running\TestRunConfiguration;
use rtens\scrut\running\TestRunner;
use rtens\scrut\TestName;
use watoki\factory\Factory;

/**
 * @property Assert $assert <-
 * @property FilesFixture files <-
 * @property ExceptionFixture try <-
 */
class ReadConfiguration {

    function before() {
        ReadConfiguration_TestRunner::$config = null;
    }

    function fromFile() {
        $this->files->givenTheFile_Containing('scrut.json', json_encode([
            'runner' => ReadConfiguration_TestRunner::class
        ]));
        $this->whenIExecuteTheCommand();

        $this->assert->isInstanceOf(ReadConfiguration_TestRunner::$config->getRunner(), ReadConfiguration_TestRunner::class);
    }

    function fromDistFile() {
        $this->files->givenTheFile_Containing('scrut.json.dist', json_encode([
            'runner' => ReadConfiguration_TestRunner::class
        ]));
        $this->whenIExecuteTheCommand();

        $this->assert->isInstanceOf(ReadConfiguration_TestRunner::$config->getRunner(), ReadConfiguration_TestRunner::class);
    }

    function fromOtherFile() {
        $this->files->givenTheFile_Containing('other.json', json_encode([
            'runner' => ReadConfiguration_TestRunner::class
        ]));
        $this->whenIExecuteTheCommand([
            '-cother.json'
        ]);

        $this->assert->isInstanceOf(ReadConfiguration_TestRunner::$config->getRunner(), ReadConfiguration_TestRunner::class);
    }

    function fromArgument() {
        $this->whenIExecuteTheCommand([
            '-c' . json_encode(['runner' => ReadConfiguration_TestRunner::class])
        ]);

        $this->assert->isInstanceOf(ReadConfiguration_TestRunner::$config->getRunner(), ReadConfiguration_TestRunner::class);
    }

    function invalidArgumentFormat() {
        $this->try->tryTo(function () {
            $this->whenIExecuteTheCommand([
                '-cInvalid'
            ]);
        });
        $this->try->thenTheException_ShouldBeThrown('Invalid configuration');
    }

    function mergeFileAndArgument() {
        $this->files->givenTheFile_Containing('scrut.json', json_encode([
            'runner' => ReadConfiguration_TestRunner::class,
            'listeners' => ['array' => ArrayListener::class],
            'listen' => ['Fail', 'Memory']
        ]));
        $this->whenIExecuteTheCommand([
            '-l' . ResultListener::class,
            '-c' . json_encode(['listen' => ['array']])
        ]);

        $listeners = ReadConfiguration_TestRunner::$config->getListeners();
        $this->assert->size($listeners, 2);
        $this->assert->isInstanceOf($listeners[0], ResultListener::class);
        $this->assert->isInstanceOf($listeners[1], ArrayListener::class);
    }

    private function whenIExecuteTheCommand($arguments = []) {
        $command = new ScrutCommand(new ConfigurationReader($this->files->fullPath(), new Factory()));
        $command->execute($arguments);
    }
}

class ReadConfiguration_TestRunner extends TestRunner {

    /** @var TestRunConfiguration */
    public static $config;

    /**
     * @param TestRunConfiguration $configuration <-
     */
    function __construct(TestRunConfiguration $configuration) {
        self::$config = $configuration;
    }

    public function run(TestName $name = null) {
        return true;
    }

}
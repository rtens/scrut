<?php
namespace rtens\scrut\running;

use rtens\scrut\TestName;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestSuite;
use watoki\factory\Factory;

/**
 * @property \rtens\scrut\fixtures\FilesFixture files <-
 * @property \rtens\scrut\Assert $then <-
 * @property \rtens\scrut\fixtures\ExceptionFixture try <-
 */
class ComposeTestSuites {

    /** @var TestSuite */
    private $test;

    function before() {
        ComposeTestSuites_TestRunner::$config = null;
    }

    function defaultSuite() {
        $this->givenTheConfiguration([]);

        $this->whenIExecuteTheCommand();

        $this->then->isInstanceOf($this->test, GenericTestSuite::class);
        $this->then->equals($this->test->getName()->toString(), 'Test');
    }

    function invalidConfiguration() {
        $this->givenTheConfiguration([
            'suite' => [
                'invalid' => 'foo'
            ]
        ]);
        $this->try->tryTo(function () {
            $this->whenIExecuteTheCommand();
        });
        $this->try->thenAnExceptionShouldBeThrown();
    }

    function genericSuite() {
        $this->givenTheConfiguration([
            'suite' => [
                'name' => 'Foo'
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->then->isInstanceOf($this->test, GenericTestSuite::class);
        $this->then->equals($this->test->getName()->toString(), 'Foo');
    }

    function fileSuite() {
        $this->givenTheConfiguration([
            'suite' => [
                'file' => 'some/file'
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->then->isInstanceOf($this->test, FileTestSuite::class);
        $this->then->equals($this->test->getName()->toString(), 'some/file');
    }

    function plainClass() {
        $this->givenTheConfiguration([
            'suite' => [
                'class' => ComposeTestSuites_PlainFoo::class
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->then->isInstanceOf($this->test, PlainTestSuite::class);
        $this->then->equals($this->test->getName()->toString(), ComposeTestSuites_PlainFoo::class);
    }

    function staticSuite() {
        $this->givenTheConfiguration([
            'suite' => [
                'class' => ComposeTestSuites_StaticFoo::class
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->then->isInstanceOf($this->test, ComposeTestSuites_StaticFoo::class);
    }

    function composeSuite() {
        $this->givenTheConfiguration([
            'suite' => [
                'name' => 'One',
                'suites' => [
                    ['class' => ComposeTestSuites_StaticFoo::class],
                    ['name' => 'Two',
                    'suites' => [
                        ['name' => 'Three']
                    ]],
                ]
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->then->size($this->test->getTests(), 2);
        $this->then->isInstanceOf($this->test->getTests()[0], ComposeTestSuites_StaticFoo::class);
        $this->then->isInstanceOf($this->test->getTests()[1], GenericTestSuite::class);
        $this->then->equals($this->test->getTests()[1]->getName()->toString(), 'One::Two');
        $this->then->size(self::suite($this->test->getTests()[1])->getTests(), 1);
        $this->then->equals(self::suite($this->test->getTests()[1])->getTests()[0]->getName()->toString(), 'One::Two::Three');
    }

    private function givenTheConfiguration(array $config) {
        $this->files->givenTheFile_Containing('scrut.json', json_encode($config));
    }

    private function whenIExecuteTheCommand() {
        $command = new ScrutCommand(new ConfigurationReader($this->files->fullPath(), new Factory()));
        $command->execute(['-c' . json_encode(["runner" => ComposeTestSuites_TestRunner::class])]);
        $this->test = ComposeTestSuites_TestRunner::$config->getTest();
    }

    /**
     * @param TestSuite|mixed $suite
     * @return TestSuite
     */
    private static function suite($suite) {
        return $suite;
    }
}

class ComposeTestSuites_TestRunner extends TestRunner {

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

class ComposeTestSuites_PlainFoo {
}

class ComposeTestSuites_StaticFoo extends StaticTestSuite {
}
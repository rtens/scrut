<?php
namespace spec\rtens\scrut;

use rtens\scrut\Assert;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\results\PassedTestResult;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestFilter;

/**
 * @property \rtens\scrut\fixtures\FilesFixture files <-
 */
class InjectDependencies {

    function __construct() {
        $this->listener = new ArrayListener();
    }

    function injectConstructor(Assert $assert) {
        $this->files->givenTheFile_Containing('inject/InjectConstructor.php', '<?php
            class InjectConstructor {
                /**
                 * @param $foo <-
                 * @param $bar <-
                 */
                function __construct(\StdClass $foo, \StdClass $bar) {
                    assert($foo && $bar);
                    $this->foo = $foo;
                }

                function foo($assert) {
                    $assert($this->foo);
                }
            }
        ');
        $this->runFileTestSuite('inject/InjectConstructor.php');

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectProperties(Assert $assert) {
        $this->files->givenTheFile_Containing('inject/InjectProperties.php', '<?php
            class InjectProperties {

                /** @var \StdClass <- */
                protected $foo;

                /** @var \StdClass */
                protected $bar;

                function foo($assert) {
                    $assert($this->foo);
                    $assert(!$this->bar);
                }
            }
        ');
        $this->runFileTestSuite('inject/InjectProperties.php');

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectAnnotations(Assert $assert) {
        $this->files->givenTheFile_Containing('inject/InjectAnnotations.php', '<?php
            /**
             * @property \StdClass $foo <-
             * @property \StdClass $bar
             */
            class InjectDependencies_InjectAnnotations {
                function foo($assert) {
                    $assert($this->foo);
                    $assert(!isset($this->bar));
                }
            }
        ');
        $this->runFileTestSuite('inject/InjectAnnotations.php');

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectPropertiesAndAnnotationsIntoStaticTestSuite(Assert $assert) {
        $this->files->givenTheFile_Containing('inject/InjectPropertiesIntoStatic.php', '<?php
            /**
             * @property \StdClass $baz <-
             * @property \StdClass $mez
             */
            class InjectPropertiesIntoStatic extends ' . StaticTestSuite::class . ' {

                /** @var \StdClass <- */
                protected $foo;

                /** @var \StdClass */
                protected $bar;

                function foo() {
                    $this->assert($this->foo);
                    $this->assert(!$this->bar);
                    $this->assert($this->baz);
                    $this->assert(!isset($this->mez));
                }
            }
        ');
        $this->runFileTestSuite('inject/InjectPropertiesIntoStatic.php');

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function asserterIsPassedToInjectedObject(Assert $assert) {
        $this->files->givenTheFile_Containing('inject/InjectAsserter.php', '<?php
            /** @property InjectedThing $that <- */
            class InjectAsserter {
                function foo() {
                    $this->that->assert->pass();
                }

                function bar() {
                }
            }

            /** @property ' . Assert::class . ' $assert <- */
            class InjectedThing {}
        ');
        $this->runFileTestSuite('inject/InjectAsserter.php');

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
        $assert->isInstanceOf($this->listener->results[1], IncompleteTestResult::class);
    }

    private function file($string) {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $string);
    }

    private function runFileTestSuite($path) {
        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath(), $this->file($path));
        $suite->run($this->listener);
    }
}
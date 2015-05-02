<?php
namespace spec\rtens\scrut;

use rtens\scrut\Asserter;
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

    function injectConstructor(Asserter $assert) {
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
        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('inject/InjectConstructor.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectProperties(Asserter $assert) {
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
        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('inject/InjectProperties.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectAnnotations(Asserter $assert) {
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
        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('inject/InjectAnnotations.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectPropertiesAndAnnotationsIntoStaticTestSuite(Asserter $assert) {
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
        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('inject/InjectPropertiesIntoStatic.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function asserterIsPassedToInjectedObject(Asserter $assert) {
        $this->files->givenTheFile_Containing('inject/InjectAsserter.php', '<?php
            /** @property InjectedThing $that <- */
            class InjectAsserter {
                function foo() {
                    $this->that->assert->pass();
                }

                function bar() {
                }
            }

            /** @property ' . Asserter::class . ' $assert <- */
            class InjectedThing {}
        ');
        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('inject/InjectAsserter.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
        $assert->isInstanceOf($this->listener->results[1], IncompleteTestResult::class);
    }
}
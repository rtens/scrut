<?php
namespace spec\rtens\scrut;

use rtens\scrut\Fixture;
use rtens\scrut\results\PassedTestResult;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestSuiteFactory;
use rtens\scrut\tests\TestFilter;
use rtens\scrut\listeners\ArrayListener;

/**
 * Fixtures can be used by the tests as delegates. They are hooked into the before/after invocations.
 *
 * @property \rtens\scrut\fixtures\FilesFixture files <-
 * @property \rtens\scrut\Assert assert <-
 */
class InjectFixtures {

    function __construct() {
        $this->listener = new ArrayListener();
    }

    function callHooksOnFixturesOfPlainSuites() {
        $this->files->givenTheFile_Containing('CallHooksOnFixture.php', '<?php
            /** @property SomeCoolFixture f <- */
            class MyTestWithFixtures {
                function before() {
                    assert($this->f->calledBefore);
                }
                function after() {
                    assert(empty($this->f->calledAfter));
                }
                function someTest() {}
            }

            /** @property SomeOtherCoolFixture f <- */
            class SomeCoolFixture extends ' . Fixture::class . '{
                public function before() {
                    $this->calledBefore = true;
                }
                public function after() {
                    $this->calledAfter = true;
                    $this->assert->isTrue($this->calledBefore);
                }
            }

            class SomeOtherCoolFixture extends ' . Fixture::class . '{
                public function before() {
                    $this->calledBefore = true;
                }
                public function after() {
                    $this->assert->isTrue($this->calledBefore);
                }
            }
        ');

        $this->runFileTestSuite('CallHooksOnFixture.php');

        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function callHooksOnFixturesOfStaticSuites() {
        $this->files->givenTheFile_Containing('CallHooksOnFixture.php', '<?php
            /** @property SomeCoolStaticFixture f <- */
            class MyStaticTestWithFixtures extends ' . StaticTestSuite::class . ' {
                function someTest() {}
            }

            class SomeCoolStaticFixture extends ' . Fixture::class . '{
                public function before() {
                    $this->calledBefore = true;
                }
                public function after() {
                    $this->assert->isTrue($this->calledBefore);
                }
            }
        ');

        $this->runFileTestSuite('CallHooksOnFixture.php');

        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    private function runFileTestSuite($path) {
        $suite = new FileTestSuite(new TestSuiteFactory(), new TestFilter(), $this->files->fullPath(), $this->file($path));
        $suite->run($this->listener);
    }

    private function file($string) {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $string);
    }
}
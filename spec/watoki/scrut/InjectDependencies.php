<?php
namespace spec\watoki\scrut;

use watoki\factory\Factory;
use watoki\factory\providers\DefaultProvider;
use watoki\scrut\Asserter;
use watoki\scrut\Fixture;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\tests\file\FileTestSuite;
use watoki\scrut\tests\statics\StaticTestSuite;

class InjectDependencies {

    function __construct() {
        $this->listener = new ArrayListener();
    }

    function injectConstructor(Asserter $assert) {
        $this->fileContent('inject/InjectConstructor.php', '<?php
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
        $suite = new FileTestSuite(new Factory(), $this->tmp('inject/InjectConstructor.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectProperties(Asserter $assert) {
        $this->fileContent('inject/InjectProperties.php', '<?php
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
        $suite = new FileTestSuite(new Factory(), $this->tmp('inject/InjectProperties.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectPropertiesIntoStaticTestSuite(Asserter $assert) {
        $this->fileContent('inject/InjectPropertiesIntoStatic.php', '<?php
            class InjectPropertiesIntoStatic extends ' . StaticTestSuite::class . ' {

                /** @var \StdClass <- */
                protected $foo;

                /** @var \StdClass */
                protected $bar;

                function foo() {
                    $this->assert($this->foo);
                    $this->assert(!$this->bar);
                }
            }
        ');
        $suite = new FileTestSuite(new Factory(), $this->tmp('inject/InjectPropertiesIntoStatic.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function injectAnnotations(Asserter $assert) {
        $this->fileContent('inject/InjectAnnotations.php', '<?php
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
        $suite = new FileTestSuite(new Factory(), $this->tmp('inject/InjectAnnotations.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function asserterIsPassedToInjectedObject(Asserter $assert) {
        $this->fileContent('inject/InjectAsserter.php', '<?php
            /** @property ' . Asserter::class . ' $assert <- */
            class InjectAsserter {
                function foo() {
                    $this->assert->pass();
                }
            }
        ');
        $suite = new FileTestSuite(new Factory(), $this->tmp('inject/InjectAsserter.php'));
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function configureFactory(Asserter $assert) {
        $factory = new Factory();
        $provider = new DefaultProvider($factory);
        $provider->setAnnotationFilter(function ($annotation) {
            return strpos($annotation, '@inject');
        });
        $factory->setProvider(\StdClass::class, $provider);

        $this->fileContent('inject/OnlyMarkedOnes.php', '<?php
            /**
             * @property ' . Fixture::class . ' $fix @inject
             * @property ' . Fixture::class . ' $not @doNotInject
             */
             class OnlyMarkedOnesStatic extends ' . StaticTestSuite::class . ' {
                function foo() {
                    $this->assert($this->fix);
                    $this->assert(!isset($this->not));
                }
             }

            /**
             * @property ' . Fixture::class . ' $fix @inject
             * @property ' . Fixture::class . ' $not @doNotInject
             */
             class OnlyMarkedOnesPlain {
                function foo($assert) {
                    $assert($this->fix);
                    $assert(!isset($this->not));
                }
             }
        ');
        $suite = new FileTestSuite($factory, $this->tmp('inject/OnlyMarkedOnes.php'), null);
        $suite->run($this->listener);

        $assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
        $assert->isInstanceOf($this->listener->results[1], PassedTestResult::class);
    }

    private function fileContent($fileName, $content) {
        $this->createFolder(dirname($fileName));
        file_put_contents($this->tmp($fileName), $content);
    }

    private function createFolder($path) {
        @mkdir($this->tmp($path), 0777, true);
    }

    private function tmp($path) {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . time() . DIRECTORY_SEPARATOR
        . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }

    public function after() {
        $this->clear($this->tmp(""));
    }

    private function clear($dir) {
        if (!file_exists($dir)) {
            return;
        }

        foreach (new \DirectoryIterator($dir) as $file) {
            if ($file->isFile()) {
                unlink($file->getRealPath());
            } else if (!$file->isDot()) {
                $this->clear($file->getRealPath());
            }
        }
        rmDir($dir);
    }
}
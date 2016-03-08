<?php
namespace spec\rtens\scrut\running;

use rtens\scrut\Assert;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\running\ConfigurationReader;
use rtens\scrut\running\ScrutCommand;
use rtens\scrut\running\TestRunConfiguration;
use rtens\scrut\running\TestRunner;
use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\TestRunListener;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestFilter;
use watoki\factory\Factory;

/**
 * Tests can be run using their names which can be a composition of files, classes, methods or generic names.
 *
 * Examples:
 *  scrut spec                  -- executes all tests in the folder "spec"
 *  scrut spec/some/File.php    -- executes all tests found in the file "File.php"
 *  scrut some\Foo              -- executes all tests of the class "Foo"
 *  scrut some\Foo::foo         -- executes only the "foo" method of the "Foo" class
 *
 * @property \rtens\scrut\fixtures\FilesFixture files <-
 */
class RunTestByName {

    /** @var GenericTestSuite */
    private $test;

    /** @var ArrayListener */
    private $listener;

    function before() {
        $this->test = new GenericTestSuite('Foo');
        $this->listener = new ArrayListener();
    }

    function runDefault(Assert $assert) {
        $this->executeCommand($assert);

        $assert->size($this->listener->started, 1);
        $assert($this->listener->started[0]->toString(), 'Foo');
    }

    function returnSuccess(Assert $assert) {
        $this->executeCommand($assert);
    }

    function invalidName(Assert $assert) {
        try {
            $this->executeCommand($assert, ['Not']);
            $assert->fail("Should have thrown an Exception");
        } catch (\InvalidArgumentException $e) {
            $assert($e->getMessage(), 'Could not resolve [Not]');
        }
    }

    function returnFailure(Assert $assert) {
        $this->test->test('bar', function (Assert $assert) {
            $assert->fail();
        });

        $this->executeCommand($assert, [], 1);
    }

    function runRoot(Assert $assert) {
        $this->executeCommand($assert, ['Foo']);

        $assert->size($this->listener->started, 1);
        $assert($this->listener->started[0]->toString(), 'Foo');
    }

    function genericName(Assert $assert) {
        $this->test->test('foo');
        $this->test->test('bar');

        $this->executeCommand($assert, ['Foo::bar']);

        $assert->size($this->listener->started, 1);
        $assert($this->listener->started[0]->toString(), 'Foo::bar');
    }

    function genericCompositeName(Assert $assert) {
        $this->test->suite('foo', function (GenericTestSuite $suite) {
            $suite->test('bar');
            $suite->test('baz');
        });

        $this->executeCommand($assert, ['Foo::foo::baz']);

        $assert($this->listener->started[0]->toString(), 'Foo::foo::baz');
    }

    function plainClassName(Assert $assert) {
        $this->executeCommand($assert, [RunTestByName_Plain::class]);

        $assert($this->listener->started[0]->toString(), RunTestByName_Plain::class);
        $assert($this->listener->started[1]->toString(), RunTestByName_Plain::class . '::foo');
        $assert($this->listener->started[2]->toString(), RunTestByName_Plain::class . '::bar');
    }

    function staticClassName(Assert $assert) {
        $this->executeCommand($assert, [RunTestByName_Static::class]);

        $assert($this->listener->started[0]->toString(), RunTestByName_Static::class);
        $assert($this->listener->started[1]->toString(), RunTestByName_Static::class . '::foo');
        $assert($this->listener->started[2]->toString(), RunTestByName_Static::class . '::bar');
    }

    function methodOfPlainSuite(Assert $assert) {
        $this->executeCommand($assert, [RunTestByName_Plain::class . '::bar']);

        $assert->size($this->listener->started, 1);
        $assert($this->listener->started[0]->toString(), RunTestByName_Plain::class . '::bar');
    }

    function methodOfStaticSuite(Assert $assert) {
        $this->executeCommand($assert, [RunTestByName_Static::class . '::bar']);

        $assert->size($this->listener->started, 1);
        $assert($this->listener->started[0]->toString(), RunTestByName_Static::class . '::bar');
    }

    function folderName(Assert $assert) {
        $this->files->givenTheFile_Containing('folder/SomeFile.php', '<?php
            class SomeClassInFolder {}
        ');
        $this->files->givenTheFile_Containing('folder/SomeOtherFile.php', '<?php
            class SomeOtherClassInFolder {}
        ');

        $this->executeCommand($assert, ['folder']);

        $assert->size($this->listener->results, 2);
    }

    function fileName(Assert $assert) {
        $this->files->givenTheFile_Containing('root/SomeFile.php', '<?php
            class SomeClassInFile {}
            class SomeOtherClassInFile {}
        ');

        $this->executeCommand($assert, ['root' . DIRECTORY_SEPARATOR . 'SomeFile.php']);

        $assert->size($this->listener->started, 3);
        $assert($this->listener->started[1]->last(), 'SomeClassInFile');
        $assert($this->listener->started[2]->last(), 'SomeOtherClassInFile');
    }

    function genericNameTrumpsFolderName(Assert $assert) {
        $this->files->givenTheFile_Containing('Foo/SomethingInHere.php', '<?php class NotThisFooThough {}');

        $this->executeCommand($assert, ['Foo']);

        $assert->size($this->listener->started, 1);
        $assert($this->listener->started[0]->toString(), 'Foo');
    }

    private function executeCommand(Assert $assert, $arguments = [], $shouldReturn = 0) {
        $factory = new Factory();

        $factory->setSingleton(
            new RunTestByName_Configuration($this->files->fullPath(), $this->test, $this->listener),
            TestRunConfiguration::class);

        $command = new ScrutCommand(new ConfigurationReader($this->files->fullPath(), $factory));
        $returned = $command->execute($arguments);

        $assert($returned, $shouldReturn);
    }
}

class RunTestByName_Configuration extends TestRunConfiguration {

    /**
     * @param string $cwd
     * @param Test $test
     * @param TestRunListener $listener
     */
    function __construct($cwd, Test $test, TestRunListener $listener) {
        parent::__construct(new Factory(), $cwd, []);
        $this->test = $test;
        $this->listener = $listener;
    }

    public function getRunner() {
        return new TestRunner($this);
    }

    public function getListeners() {
        return [$this->listener];
    }

    public function getFilter() {
        return new TestFilter();
    }

    public function getTest(TestName $parent = null) {
        return $this->test;
    }

}

class RunTestByName_Plain {

    function foo() {
    }

    function bar() {
    }
}

class RunTestByName_Static extends StaticTestSuite {

    function foo() {
    }

    function bar() {
    }
}
<?php
namespace rtens\scrut\running;

use rtens\scrut\Assert;
use rtens\scrut\cli\TestRunner;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\TestName;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestFilter;

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

    /** @var RunTestByName_TestRunner */
    private $runner;

    function before() {
        $this->runner = new RunTestByName_TestRunner($this->files->fullPath(''));
        $this->runner->test = new GenericTestSuite('Foo');
    }

    function invalidName(Assert $assert) {
        try {
            $this->runner->run(new TestName('Not'));
            $assert->fail("Should have thrown an Exception");
        } catch (\InvalidArgumentException $e) {
            $assert($e->getMessage(), 'Could not resolve [Not]');
        }
    }

    function runDefault(Assert $assert) {
        $this->runner->run();

        $assert->size($this->runner->listener->started, 1);
        $assert($this->runner->listener->started[0]->toString(), 'Foo');
    }

    function returnSuccess(Assert $assert) {
        $passed = $this->runner->run();

        $assert($passed);
    }

    function returnFailure(Assert $assert) {
        $this->runner->test->test('bar', function (Assert $assert) {
            $assert->fail();
        });

        $passed = $this->runner->run();

        $assert->not($passed);
    }

    function runRoot(Assert $assert) {
        $this->runner->run(new TestName('Foo'));

        $assert->size($this->runner->listener->started, 1);
        $assert($this->runner->listener->started[0]->toString(), 'Foo');
    }

    function genericName(Assert $assert) {
        $this->runner->test->test('foo');
        $this->runner->test->test('bar');

        $this->runner->run(new TestName('Foo', 'bar'));

        $assert->size($this->runner->listener->started, 1);
        $assert($this->runner->listener->started[0]->toString(), 'Foo::bar');
    }

    function genericCompositeName(Assert $assert) {
        $this->runner->test->suite('foo', function (GenericTestSuite $suite) {
            $suite->test('bar');
            $suite->test('baz');
        });

        $this->runner->run(new TestName('Foo', 'foo', 'baz'));

        $assert($this->runner->listener->started[0]->toString(), 'Foo::foo::baz');
    }

    function plainClassName(Assert $assert) {
        $passed = $this->runner->run(new TestName(RunTestByName_Plain::class));

        $assert($passed);
        $assert($this->runner->listener->started[0]->toString(), RunTestByName_Plain::class);
        $assert($this->runner->listener->started[1]->toString(), RunTestByName_Plain::class . '::foo');
        $assert($this->runner->listener->started[2]->toString(), RunTestByName_Plain::class . '::bar');
    }

    function staticClassName(Assert $assert) {
        $passed = $this->runner->run(new TestName(RunTestByName_Static::class));

        $assert($passed);
        $assert($this->runner->listener->started[0]->toString(), RunTestByName_Static::class);
        $assert($this->runner->listener->started[1]->toString(), RunTestByName_Static::class . '::foo');
        $assert($this->runner->listener->started[2]->toString(), RunTestByName_Static::class . '::bar');
    }

    function methodOfPlainSuite(Assert $assert) {
        $this->runner->run(new TestName(RunTestByName_Plain::class, 'bar'));

        $assert->size($this->runner->listener->started, 1);
        $assert($this->runner->listener->started[0]->toString(), RunTestByName_Plain::class . '::bar');
    }

    function methodOfStaticSuite(Assert $assert) {
        $this->runner->run(new TestName(RunTestByName_Static::class, 'bar'));

        $assert->size($this->runner->listener->started, 1);
        $assert($this->runner->listener->started[0]->toString(), RunTestByName_Static::class . '::bar');
    }

    function folderName(Assert $assert) {
        $this->files->givenTheFile_Containing('folder/SomeFile.php', '<?php
            class SomeClassInFolder {}
        ');
        $this->files->givenTheFile_Containing('folder/SomeOtherFile.php', '<?php
            class SomeOtherClassInFolder {}
        ');
        $passed = $this->runner->run(new TestName('folder'));

        $assert($passed);
        $assert->size($this->runner->listener->results, 2);
    }

    function fileName(Assert $assert) {
        $this->files->givenTheFile_Containing('root/SomeFile.php', '<?php
            class SomeClassInFile {}
            class SomeOtherClassInFile {}
        ');
        $passed = $this->runner->run(new TestName('root/SomeFile.php'));

        $assert($passed);
        $assert($this->runner->listener->started[1]->last(), 'SomeClassInFile');
        $assert($this->runner->listener->started[2]->last(), 'SomeOtherClassInFile');
    }

    function genericNameTrumpsFolderName(Assert $assert) {
        $this->files->givenTheFile_Containing('Foo/SomethingInHere.php', '<?php class NotThisFooThough {}');

        $this->runner->run(new TestName('Foo'));

        $assert->size($this->runner->listener->started, 1);
        $assert($this->runner->listener->started[0]->toString(), 'Foo');
    }
}

class RunTestByName_TestRunner extends TestRunner {

    /** @var GenericTestSuite */
    public $test;

    /** @var ArrayListener */
    public $listener;

    function __construct($cwd) {
        parent::__construct($cwd);
        $this->listener = new ArrayListener();
    }

    /**
     * @return \rtens\scrut\TestRunListener
     */
    protected function getListener() {
        $this->listener = new ArrayListener();
        return $this->listener;
    }

    /**
     * @return \rtens\scrut\Test
     */
    protected function getTest() {
        return $this->test;
    }

    /**
     * @return TestFilter
     */
    protected function createFilter() {
        return new TestFilter();
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
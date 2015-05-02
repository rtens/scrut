<?php
namespace spec\rtens\scrut;

use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\TestName;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestFilter;


/**
 * @property \rtens\scrut\fixtures\FilesFixture files <-
 */
class RunTestSuitesFromFiles extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
        $this->files->clear();
    }

    protected function after() {
        $this->files->clear();
    }

    function noExistingFolder() {
        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('some/foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function emptyFolder() {
        $this->files->givenTheFolder('some/foo');

        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('some/foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 1);
        $this->assert($this->listener->started[0]->last(), $this->files->fullPath('some/foo'));

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function loadSingleFile() {
        $this->files->givenTheFile_Containing('foo/SingleFile.php', '<?php
            class SingleFoo {}');

        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('foo/SingleFile.php'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[0]->last(), $this->files->fullPath('foo/SingleFile.php'));
        $this->assert($this->listener->started[1]->last(), 'SingleFoo');
    }

    function emptySuite() {
        $this->files->givenTheFile_Containing('foo/AnEmptyFoo.php', '<?php
            class EmptyFoo {}');

        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[1]->last(), 'EmptyFoo');

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function runStaticSuite() {
        $this->files->givenTheFile_Containing('foo/MyFoo.php', '<?php
            class Foo extends ' . StaticTestSuite::class . ' {
                function bar() {}
                function baz() {}
            }');

        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 4);
        $this->assert($this->listener->started[2]->last(), "bar");
        $this->assert($this->listener->started[3]->last(), "baz");
    }

    function loadGenericTestSuite() {
        $this->files->givenTheFile_Containing('foo/GenericFoo.php', '<?php
            class IgnoreThisOne {}
            return new ' . GenericTestSuite::class . '("Generic foo");');

        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('foo/GenericFoo.php'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[1], 'Generic foo');
    }

    function findAllSuites() {
        $this->files->givenTheFile_Containing('foo/One.php', '<?php
            class One {}
            class AnotherOne {}
        ');
        $this->files->givenTheFile_Containing('foo/Two.php', '<?php
            class Two {}
        ');

        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('foo'));
        $suite->run($this->listener);

        $names = array_map(function (TestName $test) {
            return $test->last();
        }, $this->listener->started);
        $this->assert->contains($names, "One");
        $this->assert->contains($names, "AnotherOne");
        $this->assert->contains($names, "Two");
    }

    function findTestSuitesInSubFolders() {
        $this->files->givenTheFile_Containing('foo/one/One.php', '<?php
            class OneOne {}
        ');
        $this->files->givenTheFile_Containing('foo/one/Two.php', '<?php
            class OneTwo {}
        ');
        $this->files->givenTheFile_Containing('foo/two/One.php', '<?php
            class TwoOne {}
        ');

        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 4);

        $names = array_map(function (TestName $test) {
            return $test->last();
        }, $this->listener->started);
        $this->assert->contains($names, "OneOne");
        $this->assert->contains($names, "OneTwo");
        $this->assert->contains($names, "TwoOne");
    }

    function filterClasses() {
        $this->files->givenTheFile_Containing('foo/ThisOne.php', '<?php
            class ThisOne {}
        ');
        $this->files->givenTheFile_Containing('foo/NotThisOne.php', '<?php
            class NotThisOne {}
        ');

        $suite = new FileTestSuite((new TestFilter())
            ->filterClass(function (\ReflectionClass $class) {
                return $class->getName() == 'ThisOne';
            }), $this->files->fullPath('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[1]->last(), 'ThisOne');
    }

    function filterFiles() {
        $this->files->givenTheFile_Containing('foo/ThisYes.php', '<?php
            class ThisOneYes {}
        ');
        $this->files->givenTheFile_Containing('foo/ThisNot.php', '<?php
            class ThisOneNot {}
        ');

        $suite = new FileTestSuite((new TestFilter())
            ->filterFile(function ($file) {
                return strpos($file, 'Yes.php');
            }), $this->files->fullPath('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[1]->last(), 'ThisOneYes');
    }

    function discardParentName() {
        $suite = new FileTestSuite(new TestFilter(), 'some/foo', new TestName("Foo"));
        $suite->run($this->listener);

        $this->assert($this->listener->started[0]->toString(), 'some/foo');
    }

    function ignoreAbstractClasses() {
        $this->files->givenTheFile_Containing('foo/SomethingAbstract.php', '<?php
            abstract class SomeReallyAbstractClass {
                function foo() {}
            }
        ');

        $suite = new FileTestSuite(new TestFilter(), $this->files->fullPath('foo/SomethingAbstract.php'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 1);
    }
}

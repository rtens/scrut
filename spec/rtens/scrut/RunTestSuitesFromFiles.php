<?php
namespace spec\rtens\scrut;

use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\TestName;
use rtens\scrut\tests\file\FileTestSuite;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestFilter;

class RunTestSuitesFromFiles extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
        $this->after();
    }

    function noExistingFolder() {
        $suite = new FileTestSuite(new TestFilter(), $this->tmp('some/foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function emptyFolder() {
        $this->createFolder('some/foo');

        $suite = new FileTestSuite(new TestFilter(), $this->tmp('some/foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 1);
        $this->assert($this->listener->started[0]->last(), $this->tmp('some/foo'));

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function loadSingleFile() {
        $this->fileContent('foo/SingleFile.php', '<?php
            class SingleFoo {}');

        $suite = new FileTestSuite(new TestFilter(), $this->tmp('foo/SingleFile.php'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[0]->last(), $this->tmp('foo/SingleFile.php'));
        $this->assert($this->listener->started[1]->last(), 'SingleFoo');
    }

    function emptySuite() {
        $this->fileContent('foo/AnEmptyFoo.php', '<?php
            class EmptyFoo {}');

        $suite = new FileTestSuite(new TestFilter(), $this->tmp('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[1]->last(), 'EmptyFoo');

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function runStaticSuite() {
        $this->fileContent('foo/MyFoo.php', '<?php
            class Foo extends ' . StaticTestSuite::class . ' {
                function bar() {}
                function baz() {}
            }');

        $suite = new FileTestSuite(new TestFilter(), $this->tmp('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 4);
        $this->assert($this->listener->started[2]->last(), "bar");
        $this->assert($this->listener->started[3]->last(), "baz");
    }

    function loadGenericTestSuite() {
        $this->fileContent('foo/GenericFoo.php', '<?php
            class IgnoreThisOne {}
            return new ' . GenericTestSuite::class . '("Generic foo");');

        $suite = new FileTestSuite(new TestFilter(), $this->tmp('foo/GenericFoo.php'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[1], 'Generic foo');
    }

    function findAllSuites() {
        $this->fileContent('foo/One.php', '<?php
            class One {}
            class AnotherOne {}
        ');
        $this->fileContent('foo/Two.php', '<?php
            class Two {}
        ');

        $suite = new FileTestSuite(new TestFilter(), $this->tmp('foo'));
        $suite->run($this->listener);

        $names = array_map(function (TestName $test) {
            return $test->last();
        }, $this->listener->started);
        $this->assert->contains($names, "One");
        $this->assert->contains($names, "AnotherOne");
        $this->assert->contains($names, "Two");
    }

    function findTestSuitesInSubFolders() {
        $this->fileContent('foo/one/One.php', '<?php
            class OneOne {}
        ');
        $this->fileContent('foo/one/Two.php', '<?php
            class OneTwo {}
        ');
        $this->fileContent('foo/two/One.php', '<?php
            class TwoOne {}
        ');

        $suite = new FileTestSuite(new TestFilter(), $this->tmp('foo'));
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
        $this->fileContent('foo/ThisOne.php', '<?php
            class ThisOne {}
        ');
        $this->fileContent('foo/NotThisOne.php', '<?php
            class NotThisOne {}
        ');

        $suite = new FileTestSuite((new TestFilter())
            ->filterClass(function (\ReflectionClass $class) {
                return $class->getName() == 'ThisOne';
            }), $this->tmp('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[1]->last(), 'ThisOne');
    }

    function filterFiles() {
        $this->fileContent('foo/ThisYes.php', '<?php
            class ThisOneYes {}
        ');
        $this->fileContent('foo/ThisNot.php', '<?php
            class ThisOneNot {}
        ');

        $suite = new FileTestSuite((new TestFilter())
            ->filterFile(function ($file) {
                return strpos($file, 'Yes.php');
            }), $this->tmp('foo'));
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
        $this->fileContent('foo/SomethingAbstract.php', '<?php
            abstract class SomeReallyAbstractClass {
                function foo() {}
            }
        ');

        $suite = new FileTestSuite(new TestFilter(), $this->tmp('foo/SomethingAbstract.php'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 1);
    }

    private function fileContent($fileName, $content) {
        $this->createFolder(dirname($fileName));
        file_put_contents($this->tmp($fileName), $content);
    }

    private function createFolder($path) {
        @mkdir($this->tmp($path), 0777, true);
    }

    private function tmp($path) {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . "scrut_tmp" . DIRECTORY_SEPARATOR
        . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }

    protected function after() {
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

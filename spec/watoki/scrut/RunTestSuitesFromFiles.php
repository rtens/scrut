<?php
namespace spec\watoki\scrut;

use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\Test;
use watoki\scrut\tests\DirectoryTestSuite;
use watoki\scrut\tests\StaticTestSuite;

class RunTestSuitesFromFiles extends StaticTestSuite {

    /** @var string */
    private $tmpDir;

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
        $this->tmpDir = $tmpDir = __DIR__ . DIRECTORY_SEPARATOR . 'scrut_tmp';
        $this->after();
    }

    function noExistingFolder() {
        $suite = new DirectoryTestSuite($this->tmp('some/foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function emptyFolder() {
        $this->createFolder('some/foo');

        $suite = new DirectoryTestSuite($this->tmp('some/foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 1);
        $this->assert($this->listener->started[0]->getName(), 'foo');

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function emptySuite() {
        $this->fileContent('foo/AnEmptyFoo.php', '<?php
            class EmptyFoo {}');

        $suite = new DirectoryTestSuite($this->tmp('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert($this->listener->started[1]->getName(), 'EmptyFoo');

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function runStaticSuite() {
        $this->fileContent('foo/MyFoo.php', '<?php
            class Foo extends \watoki\scrut\tests\StaticTestSuite {
                function bar() {}
                function baz() {}
            }');

        $suite = new DirectoryTestSuite($this->tmp('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 4);
        $this->assert($this->listener->started[2]->getName(), "bar");
        $this->assert($this->listener->started[3]->getName(), "baz");
    }

    function findAllSuites() {
        $this->fileContent('foo/One.php', '<?php
            class One {}
            class AnotherOne {}
        ');
        $this->fileContent('foo/Two.php', '<?php
            class Two {}
        ');

        $suite = new DirectoryTestSuite($this->tmp('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 4);
        $this->assert($this->listener->started[1]->getName(), "One");
        $this->assert($this->listener->started[2]->getName(), "AnotherOne");
        $this->assert($this->listener->started[3]->getName(), "Two");
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

        $suite = new DirectoryTestSuite($this->tmp('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 4);

        $names = array_map(function (Test $test) {
                return $test->getName();
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

        $suite = new DirectoryTestSuite($this->tmp('foo'));
        $suite->setClassFilter(function (\ReflectionClass $class) {
            return $class->getShortName() == 'ThisOne';
        });
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
    }

    function changeName() {
        $suite = new DirectoryTestSuite($this->tmp('some/foo'), "bar");
        $suite->run($this->listener);

        $this->assert($this->listener->started[0]->getName(), 'bar');
    }

    private function fileContent($fileName, $content) {
        $this->createFolder(dirname($fileName));
        file_put_contents($this->tmp($fileName), $content);
    }

    private function createFolder($path) {
        @mkdir($this->tmp($path), 0777, true);
    }

    private function tmp($path) {
        return $this->tmpDir . DIRECTORY_SEPARATOR . $path;
    }

    protected function after() {
        $this->clear($this->tmpDir);
    }

    private function clear($dir) {
        if (!file_exists($dir)) {
            return;
        }

        foreach (new \DirectoryIterator($dir) as $file) {
            if ($file->isFile()) {
                @unlink($file->getRealPath());
            } else if (!$file->isDot()) {
                $this->clear($file->getRealPath());
            }
        }
        @rmDir($dir);
    }
}
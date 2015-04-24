<?php
namespace spec\watoki\scrut;

use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\IncompleteTestResult;
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

    function emptyFolder() {
        $this->createFolder('some/foo');

        $suite = new DirectoryTestSuite($this->tmp('some/foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 1);
        $this->assert($this->listener->started[0]->getName(), 'foo');

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function emptyStaticSuite() {
        $this->fileContent('foo/AnEmptyFoo.php', '<?php
            class EmptyFoo extends \watoki\scrut\tests\StaticTestSuite {}');

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
            class One extends \watoki\scrut\tests\StaticTestSuite {}
            class AnotherOne extends \watoki\scrut\tests\StaticTestSuite {}
        ');
        $this->fileContent('foo/Two.php', '<?php
            class Two extends \watoki\scrut\tests\StaticTestSuite {}
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
            class OneOne extends \watoki\scrut\tests\StaticTestSuite {}
        ');
        $this->fileContent('foo/one/Two.php', '<?php
            class OneTwo extends \watoki\scrut\tests\StaticTestSuite {}
        ');
        $this->fileContent('foo/two/One.php', '<?php
            class TwoOne extends \watoki\scrut\tests\StaticTestSuite {}
        ');

        $suite = new DirectoryTestSuite($this->tmp('foo'));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 4);
        $this->assert($this->listener->started[1]->getName(), "OneOne");
        $this->assert($this->listener->started[2]->getName(), "OneTwo");
        $this->assert($this->listener->started[3]->getName(), "TwoOne");
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
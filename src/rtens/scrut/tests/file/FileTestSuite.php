<?php
namespace rtens\scrut\tests\file;

use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestFilter;
use rtens\scrut\tests\TestSuite;

class FileTestSuite extends TestSuite {

    /** @var string */
    private $path;

    /** @var TestFilter */
    private $filter;

    /**
     * @param TestFilter $filter
     * @param string $path Directory of file
     * @param null|TestName $parent
     */
    function __construct(TestFilter $filter, $path, TestName $parent = null) {
        parent::__construct($parent);
        $this->path = $path;
        $this->filter = $filter;
    }

    /**
     * @return TestName
     */
    public function getName() {
        return new TestName($this->path);
    }

    /**
     * @return Test[]
     */
    protected function getTests() {
        if (!file_exists($this->path)) {
            return [];
        }
        return $this->loadTests($this->path);
    }

    private function loadTests($path) {
        if (is_file($path)) {
            return $this->loadTestsFromFile($path);
        } else {
            return $this->loadTestsFromDirectory($path);
        }
    }

    private function loadTestsFromDirectory($path) {
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            if ($fileInfo->isDir()) {
                foreach ($this->loadTestsFromDirectory($fileInfo->getRealPath()) as $test) {
                    yield $test;
                }
            } else {
                foreach ($this->loadTestsFromFile($fileInfo->getRealPath()) as $test) {
                    yield $test;
                }
            }
        }
    }

    private function loadTestsFromFile($path) {
        $before = get_declared_classes();

        /** @noinspection PhpIncludeInspection */
        $returned = include_once($path);

        if (is_a($returned, Test::class)) {
            yield $returned;
            return;
        }

        $newClasses = array_diff(get_declared_classes(), $before);
        foreach ($newClasses as $className) {
            $class = new \ReflectionClass($className);

            if (!$this->isAcceptable($class, $path)) {
                continue;
            }

            yield $this->createInstance($class);
        }
    }

    /**
     * @param \ReflectionClass $class
     * @return TestSuite
     */
    protected function createInstance(\ReflectionClass $class) {
        if ($class->isSubclassOf(StaticTestSuite::class)) {
            return $class->newInstance($this->filter, $this->getName());
        } else {
            return new PlainTestSuite($this->filter, $class->getName(), $this->getName());
        }
    }

    private function isAcceptable(\ReflectionClass $class, $path) {
        return $class->getFileName() == $path
        && !$class->isAbstract()
        && $this->filter->acceptsFile($path)
        && $this->filter->acceptsClass($class);
    }

    /**
     * @return \rtens\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new FileFailureSourceLocator($this->path);
    }
}
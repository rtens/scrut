<?php
namespace watoki\scrut\tests\file;

use watoki\factory\Factory;
use watoki\scrut\Test;
use watoki\scrut\TestName;
use watoki\scrut\tests\plain\PlainTestSuite;
use watoki\scrut\tests\statics\StaticTestSuite;
use watoki\scrut\tests\TestSuite;

class FileTestSuite extends TestSuite {

    /** @var string */
    private $path;

    /** @var callable */
    private $classFilter;

    /**
     * @param string $path Directory of file
     * @param null|TestName $parent
     * @param Factory $factory
     */
    function __construct($path, TestName $parent = null, Factory $factory = null) {
        parent::__construct($parent, $factory);
        $this->path = $path;
        $this->classFilter = function () {
            return true;
        };
    }

    /**
     * @return TestName
     */
    public function getName() {
        return new TestName($this->path);
    }

    /**
     * @param callable $filter Is invoked with \ReflectionClass
     * @return $this
     */
    public function setClassFilter(callable $filter) {
        $this->classFilter = $filter;
        return $this;
    }

    /**
     * @return callable
     */
    public function getClassFilter() {
        return $this->classFilter;
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
        foreach ($newClasses as $class) {
            if (!$this->isAcceptable($class, $path)) {
                continue;
            }


            if (is_subclass_of($class, StaticTestSuite::class)) {
                $instance = $this->factory->getInstance($class, [$this->getName(), $this->factory]);
                yield $instance;
            } else {
                $instance = $this->factory->getInstance($class);
                yield new PlainTestSuite($instance, $this->getName(), $this->factory);
            }
        }
    }

    private function isAcceptable($class, $path) {
        $reflection = new \ReflectionClass($class);

        return $reflection->getFileName() == $path
            && call_user_func($this->classFilter, $reflection);
    }

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new FileFailureSourceLocator($this->path);
    }
}
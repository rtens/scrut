<?php
namespace watoki\scrut\tests;

use watoki\scrut\Test;

class DirectoryTestSuite extends TestSuite {

    /** @var string */
    private $path;

    /** @var callable */
    private $classFilter;

    /** @var string */
    private $name;

    /**
     * @param string $path Directory of file
     * @param null|string $name Defaults to directory base name
     */
    function __construct($path, $name = null) {
        $this->path = $path;
        $this->name = $name ?: basename($path);
        $this->classFilter = function () {
            return true;
        };
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
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
        $tests = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            if ($fileInfo->isDir()) {
                $tests = array_merge($tests, $this->loadTestsFromDirectory($fileInfo->getRealPath()));
            } else {
                $tests = array_merge($tests, $this->loadTestsFromFile($fileInfo->getRealPath()));
            }
        }
        return $tests;
    }

    private function loadTestsFromFile($path) {
        $tests = [];

        $before = get_declared_classes();

        /** @noinspection PhpIncludeInspection */
        $returned = include_once($path);

        if (is_a($returned, Test::class)) {
            return [$returned];
        }

        $newClasses = array_diff(get_declared_classes(), $before);
        foreach ($newClasses as $class) {
            if (!$this->isAcceptable($class, $path)) {
                continue;
            }

            if (is_subclass_of($class, StaticTestSuite::class)) {
                $tests[] = new $class();
            } else {
                $tests[] = new PlainTestSuite(new $class());
            }
        }

        return $tests;
    }

    private function isAcceptable($class, $path) {
        $reflection = new \ReflectionClass($class);

        return $reflection->getFileName() == $path
            && call_user_func($this->classFilter, $reflection);
    }
}
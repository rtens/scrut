<?php
namespace watoki\scrut\tests;

use watoki\scrut\Test;

class DirectoryTestSuite extends TestSuite {

    /** @var string */
    private $directory;

    /** @var callable */
    private $classFilter;

    /**
     * @param string $directory
     */
    function __construct($directory) {
        $this->directory = $directory;
        $this->classFilter = function () {
            return true;
        };
    }

    /**
     * @return string
     */
    public function getName() {
        return basename($this->directory);
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
        return $this->loadTests($this->directory);
    }

    private function loadTests($path) {
        $suites = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            if ($fileInfo->isDir()) {
                $suites = array_merge($suites, $this->loadTests($fileInfo->getRealPath()));
                continue;
            }

            $before = get_declared_classes();

            /** @noinspection PhpIncludeInspection */
            require_once($fileInfo->getRealPath());

            $newClasses = array_diff(get_declared_classes(), $before);

            foreach ($newClasses as $class) {
                if (call_user_func($this->classFilter, new \ReflectionClass($class))
                    && is_subclass_of($class, StaticTestSuite::class)
                ) {
                    /** @var StaticTestSuite $suite */
                    $suites[] = new $class();
                }
            }
        }
        return $suites;
    }
}
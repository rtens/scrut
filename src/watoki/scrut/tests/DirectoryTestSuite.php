<?php
namespace watoki\scrut\tests;

use watoki\scrut\Test;

class DirectoryTestSuite extends TestSuite {

    /** @var string */
    private $directory;

    /**
     * @param string $directory
     */
    function __construct($directory) {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getName() {
        return basename($this->directory);
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
                if (is_subclass_of($class, StaticTestSuite::class)) {
                    /** @var StaticTestSuite $suite */
                    $suites[] = new $class();
                }
            }
        }
        return $suites;
    }
}
<?php
namespace rtens\scrut\tests;

class TestFilter {

    /** @var null|callable */
    private $class;

    /** @var null|callable */
    private $method;

    /** @var null|callable */
    private $file;

    public function acceptsFile($path) {
        return $this->accepts($this->file, [$path]);
    }

    public function acceptsClass(\ReflectionClass $class) {
        return $this->accepts($this->class, [$class]);
    }

    public function acceptsMethod(\ReflectionMethod $method) {
        return $this->accepts($this->method, [$method]);
    }

    private function accepts($callback, $args) {
        return !$callback || call_user_func_array($callback, $args);
    }

    public function filterFile(callable $filter) {
        $this->file = $filter;
        return $this;
    }

    public function filterClass(callable $filter) {
        $this->class = $filter;
        return $this;
    }

    public function filterMethod(callable $filter) {
        $this->method = $filter;
        return $this;
    }
}
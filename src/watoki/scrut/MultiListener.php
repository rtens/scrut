<?php
namespace watoki\scrut;

class MultiListener implements ScrutinizeListener {

    /** @var array|ScrutinizeListener[] */
    private $listeners = [];

    public function add(ScrutinizeListener $listener) {
        $this->listeners[] = $listener;
    }

    public function started($name) {
        foreach ($this->listeners as $listener) {
            $listener->started($name);
        }
    }

    public function finished($name, TestResult $result) {
        foreach ($this->listeners as $listener) {
            $listener->finished($name, $result);
        }
    }
}
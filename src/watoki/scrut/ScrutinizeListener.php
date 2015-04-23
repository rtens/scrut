<?php
namespace watoki\scrut;

interface ScrutinizeListener {

    public function started($name);

    public function finished($name, TestResult $result);
}
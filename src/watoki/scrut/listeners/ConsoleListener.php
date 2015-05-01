<?php
namespace watoki\scrut\listeners;

abstract class ConsoleListener extends CollectingListener {

    protected function printLine($text = "") {
        $this->print_($text . PHP_EOL);
    }

    protected function print_($text = "") {
        echo $text;
    }

    protected function printNotEmptyLine($string) {
        if (!trim($string)) {
            return;
        }
        $this->printLine($string);
    }
}
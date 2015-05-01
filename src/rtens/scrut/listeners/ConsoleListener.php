<?php
namespace rtens\scrut\listeners;

abstract class ConsoleListener extends CollectingListener {

    /** @var callable */
    private $printer;

    function __construct(callable $printer = null) {
        $this->printer = $printer ?: function ($text) {
            echo $text;
        };
    }

    protected function printLine($text = "") {
        $this->print_($text . PHP_EOL);
    }

    protected function print_($text = "") {
        call_user_func($this->printer, $text);
    }

    protected function printNotEmptyLine($string) {
        if (!trim($string)) {
            return;
        }
        $this->printLine($string);
    }
}
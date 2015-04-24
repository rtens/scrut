<?php

use watoki\scrut\listeners\ConsoleListener;
use watoki\scrut\tests\DirectoryTestSuite;

require_once __DIR__ . '/bootstrap.php';

(new DirectoryTestSuite(__DIR__ . '/spec'))
    ->setClassFilter(function (ReflectionClass $class) {
        return strpos($class->getShortName(), '_') === false;
    })
    ->run(new ConsoleListener());
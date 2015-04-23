<?php

require_once __DIR__ . '/bootstrap.php';

use watoki\scrut\listeners\ConsoleListener;
use watoki\scrut\Scrutinizer;

(new Scrutinizer())
    ->listen(new ConsoleListener())
    ->add(new \spec\watoki\scrut\RunDynamicTestSuite())
    ->add(new \spec\watoki\scrut\RunStaticTestSuite())
    ->add(new \spec\watoki\scrut\RunTestSuitesFromFiles())
    ->add(new \spec\watoki\scrut\RunFromConsole())
    ->add(new \spec\watoki\scrut\InjectProperties())
    ->run();
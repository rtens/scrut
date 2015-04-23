<?php

use watoki\scrut\listeners\ConsoleListener;
use watoki\scrut\tests\GenericTestSuite;

require_once __DIR__ . '/bootstrap.php';

(new GenericTestSuite("scrut"))
    ->add(new \spec\watoki\scrut\RunDynamicTestSuite())
    ->add(new \spec\watoki\scrut\RunStaticTestSuite())
    ->add(new \spec\watoki\scrut\RunTestSuitesFromFiles())
    ->add(new \spec\watoki\scrut\RunFromConsole())
    ->add(new \spec\watoki\scrut\InjectProperties())
    ->run(new ConsoleListener());
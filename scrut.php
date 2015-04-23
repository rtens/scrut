<?php

require_once __DIR__ . '/bootstrap.php';

use spec\watoki\scrut\RunDynamicTestSuite;
use watoki\scrut\listeners\ConsoleListener;
use watoki\scrut\Scrutinizer;

$s = new Scrutinizer();
$s->listen(new ConsoleListener());
$s->add(new RunDynamicTestSuite());
$s->run();
<?php
namespace watoki\scrut;

interface Test {

    /**
     * @param TestRunListener $listener
     * @return void
     */
    public function run(TestRunListener $listener);

    /**
     * @return string
     */
    public function getName();
} 
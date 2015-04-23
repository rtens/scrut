<?php
namespace watoki\scrut;

interface Assertion {

    /**
     * @return string
     */
    public function describeFailure();

    /**
     * @return bool
     */
    public function checksOut();

}
<?php
namespace rtens\scrut\assertions;

use rtens\scrut\Assertion;
use watoki\reflect\ValuePrinter;

abstract class ValueAssertion implements Assertion {

    protected function export($value) {
        return ValuePrinter::serialize($value);
    }
}
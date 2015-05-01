<?php
namespace rtens\scrut\assertions;

use rtens\scrut\Assertion;

abstract class ValueAssertion implements Assertion {

    protected function export($value) {
        if (is_array($value) || $value instanceof \Traversable) {
            $withKeys = [];
            $values = [];
            $onlyNumericKeys = true;
            foreach ($value as $key => $item) {
                $onlyNumericKeys = $onlyNumericKeys && is_int($key);

                $exported = $this->export($item);
                $withKeys[] = $this->export($key) . ' => ' . $exported;
                $values[] = $exported;
            }
            return (is_object($value) ? '<' . get_class($value) . '>' : '')
                . '[' . implode(', ', $onlyNumericKeys ? $values : $withKeys) . ']';

        } else if (is_object($value)) {
            return '<' . get_class($value) . '>';
        } else if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } else {
            return var_export($value, true);
        }
    }
}
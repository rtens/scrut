<?php
namespace watoki\scrut;

class TestName {

    const ESCAPE_CHARACTER = '$';
    const SEPARATOR = '::';

    private $parts;

    /**
     * @param array|string... $parts
     */
    function __construct($parts) {
        $this->parts = is_array($parts) ? $parts : func_get_args();
    }

    public function with($childName) {
        return new TestName(array_merge($this->parts, [$childName]));
    }

    public function part($index) {
        if ($index < 0) {
            $index = count($this->parts) + $index;
        }
        return $this->parts[$index];
    }

    public function last() {
        return $this->part(-1);
    }

    public function __toString() {
        return $this->toString();
    }

    public function toString() {
        $escape = self::escape();
        return implode(self::SEPARATOR, array_map(function ($part) use ($escape) {
            return str_replace(array_keys($escape), array_values($escape), $part);
        }, $this->parts));
    }

    private static function escape() {
        return [
            self::ESCAPE_CHARACTER => self::ESCAPE_CHARACTER . self::ESCAPE_CHARACTER,
            ':' => self::ESCAPE_CHARACTER . ':',
        ];
    }

    public static function parse($string) {
        $escape = array_reverse(self::escape());
        $parts = array_map(function ($part) use ($escape) {
            return str_replace(array_values($escape), array_keys($escape), strrev($part));
        }, array_reverse(explode(self::SEPARATOR, strrev($string))));

        return new TestName($parts);
    }

}
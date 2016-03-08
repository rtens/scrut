<?php
namespace spec\rtens\scrut;

use rtens\scrut\Assert;
use rtens\scrut\TestName;

class ComposeTestNames {

    function emptyName(Assert $assert) {
        $name = new TestName([]);
        $assert($name->toString(), '');
    }

    function createFromString(Assert $assert) {
        $name = new TestName("foo");
        $assert($name->toString(), "foo");
    }

    function createFromArray(Assert $assert) {
        $name = new TestName(['foo', 'bar']);
        $assert($name->toString(), "foo::bar");
    }

    function createFromStrings(Assert $assert) {
        $name = new TestName('foo', 'bar');
        $assert($name->toString(), "foo::bar");
    }

    function addStrings(Assert $assert) {
        $name = new TestName('foo', 'bar');
        $composed = $name->with('baz');
        $assert($name->toString(), "foo::bar");
        $assert($composed->toString(), "foo::bar::baz");
    }

    function escapeSeparators(Assert $assert) {
        $name = new TestName('foo::bar::', ':b$az');
        $assert($name->toString(), 'foo$:$:bar$:$:::$:b$$az');
    }

    function accessParts(Assert $assert) {
        $name = new TestName('one', 'two', 'three');

        $assert($name->part(0), 'one');
        $assert($name->part(1), 'two');
        $assert($name->part(-2), 'two');
        $assert($name->last(), 'three');
    }

    function parseStrings(Assert $assert) {
        $name = TestName::parse('foo::bar::baz');
        $assert($name->toString(), 'foo::bar::baz');
    }

    function parseEscapedStrings(Assert $assert) {
        $name = TestName::parse('foo$:$:bar$:$:::$:b$$az');
        $assert($name->part(0), 'foo::bar::');
        $assert($name->part(1), ':b$az');
    }
}
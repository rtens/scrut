<?php
namespace spec\rtens\scrut;

use rtens\scrut\Asserter;
use rtens\scrut\TestName;

class ComposeTestNames {

    function emptyName(Asserter $assert) {
        $name = new TestName([]);
        $assert($name->toString(), '');
    }

    function createFromString(Asserter $assert) {
        $name = new TestName("foo");
        $assert($name->toString(), "foo");
    }

    function createFromArray(Asserter $assert) {
        $name = new TestName(['foo', 'bar']);
        $assert($name->toString(), "foo::bar");
    }

    function createFromStrings(Asserter $assert) {
        $name = new TestName('foo', 'bar');
        $assert($name->toString(), "foo::bar");
    }

    function addStrings(Asserter $assert) {
        $name = new TestName('foo', 'bar');
        $composed = $name->with('baz');
        $assert($name->toString(), "foo::bar");
        $assert($composed->toString(), "foo::bar::baz");
    }

    function escapeSeparators(Asserter $assert) {
        $name = new TestName('foo::bar::', ':b$az');
        $assert($name->toString(), 'foo$:$:bar$:$:::$:b$$az');
    }

    function accessParts(Asserter $assert) {
        $name = new TestName('one', 'two', 'three');

        $assert($name->part(0), 'one');
        $assert($name->part(1), 'two');
        $assert($name->part(-2), 'two');
        $assert($name->last(), 'three');
    }

    function parseStrings(Asserter $assert) {
        $name = TestName::parse('foo::bar::baz');
        $assert($name->toString(), 'foo::bar::baz');
    }

    function parseEscapedStrings(Asserter $assert) {
        $name = TestName::parse('foo$:$:bar$:$:::$:b$$az');
        $assert($name->part(0), 'foo::bar::');
        $assert($name->part(1), ':b$az');
    }
}
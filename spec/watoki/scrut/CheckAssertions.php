<?php
namespace spec\watoki\scrut;

use watoki\scrut\Asserter;
use watoki\scrut\assertions\ContainsAssertion;
use watoki\scrut\assertions\IsEqualAssertion;
use watoki\scrut\assertions\IsInstanceOfAssertion;
use watoki\scrut\assertions\IsTrueAssertion;
use watoki\scrut\assertions\NotAssertion;
use watoki\scrut\assertions\SizeAssertion;
use watoki\scrut\failures\AssertionFailedFailure;
use watoki\scrut\tests\statics\StaticTestSuite;

class CheckAssertions extends StaticTestSuite {

    /** @var null|AssertionFailedFailure */
    private $failure;

    function assertSomething() {
        $assert = new Asserter();
        $assert(true);
        $assert("foo");

        try {
            $assert("");
            $this->fail("Should have failed");
        } catch (AssertionFailedFailure $f) {
        }

        try {
            $assert(null);
            $this->fail("Should have failed");
        } catch (AssertionFailedFailure $f) {
        }
    }

    function assertingTheOpposite() {
        $this->assert->not()->isTrue(false);
        $this->assert->not()->equals("foo", "bar");

        $assertion = new NotAssertion(new IsEqualAssertion('a', 'a'));
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), "'a' should not equal 'a'");
    }

    function assertSomethingIsTrue() {
        $assertion = new IsTrueAssertion(true);
        $this->assert($assertion->checksOut());

        $assertion = new IsTrueAssertion("not true");
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), "'not true' should be TRUE");
    }

    function assertThingsAreEqual() {
        $assertion = new IsEqualAssertion("foo", "foo");
        $this->assert($assertion->checksOut());

        $assertion = new IsEqualAssertion("1", 1.0);
        $this->assert($assertion->checksOut());

        $assertion = new IsEqualAssertion(new \DateTime(), new \DateTime());
        $this->assert($assertion->checksOut());

        $assertion = new IsEqualAssertion("foo", "bar");
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), "'foo' should equal 'bar'");
    }

    function assertInstanceOfSomething() {
        $assertion = new IsInstanceOfAssertion(new \DateTime(), \DateTime::class);
        $this->assert($assertion->checksOut());

        $assertion = new IsInstanceOfAssertion(new \DateTime(), \DateTimeInterface::class);
        $this->assert($assertion->checksOut());

        $assertion = new IsInstanceOfAssertion("foo", \DateTime::class);
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), "'foo' should be an object");

        $assertion = new IsInstanceOfAssertion(new \StdClass(), \DateTime::class);
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), '<stdClass> should be a <DateTime>');
    }

    function assertSomethingContainsSomething() {
        $assertion = new ContainsAssertion("foobar", "oob");
        $this->assert($assertion->checksOut());

        $assertion = new ContainsAssertion(["foo", "bar"], "foo");
        $this->assert($assertion->checksOut());

        $stack = new \SplStack();
        $stack->push("foo");
        $stack->push("bar");
        $assertion = new ContainsAssertion($stack, "bar");
        $this->assert($assertion->checksOut());

        $object = new \StdClass;
        $object->property = "foo";
        $assertion = new ContainsAssertion($object, "foo");
        $this->assert($assertion->checksOut());

        $assertion = new ContainsAssertion(true, "foo");
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), "TRUE should contain 'foo'");
    }

    function assertSizeOfSomething() {
        $assertion = new SizeAssertion(["foo", "bar"], 2);
        $this->assert($assertion->checksOut());

        $stack = new \SplStack();
        $stack->push("foo");
        $stack->push("bar");
        $assertion = new SizeAssertion($stack, 2);
        $this->assert($assertion->checksOut());

        $assertion = new SizeAssertion("foo", 3);
        $this->assert($assertion->checksOut());

        $assertion = new SizeAssertion(true, 1);
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), "TRUE should be countable");

        $assertion = new SizeAssertion("foo", 1);
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), "'foo' should have length 1");

        $assertion = new SizeAssertion(["foo", "bar"], 3);
        $this->assert(!$assertion->checksOut());
        $this->assert($assertion->describeFailure(), "Counted size 2 should be 3");
    }

    function printComplexValues() {
        $assertion = new IsTrueAssertion(["foo", "bar" => "baz"]);
        $this->assert($assertion->describeFailure(), "[0 => 'foo', 'bar' => 'baz'] should be TRUE");

        $assertion = new IsTrueAssertion(new \DateTime());
        $this->assert($assertion->describeFailure(), "<DateTime> should be TRUE");

        $stack = new \SplStack();
        $stack->push(["foo", "bar"]);
        $stack->push(new \DateTime());
        $assertion = new IsTrueAssertion($stack);
        $this->assert($assertion->describeFailure(), "<SplStack>[<DateTime>, ['foo', 'bar']] should be TRUE");
    }

    function somethingIsNull() {
        $this->tryTo(function () {
            $this->assert->isNull("not");
        });
        $this->assertFailureMessage("'not' should be NULL");

        $this->assert->isNull(null);
    }

    private function assertFailureMessage($message) {
        $this->assert->not()->isNull($this->failure);
        $this->assert($this->failure->getFailureMessage(), $message);
    }

    private function tryTo(callable $do) {
        try {
            $do();
        } catch (AssertionFailedFailure $f) {
            $this->failure = $f;
        }
    }
}
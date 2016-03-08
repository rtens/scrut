<?php
namespace spec\rtens\scrut;

use rtens\scrut\failures\AssertionFailedFailure;
use rtens\scrut\TestName;
use rtens\scrut\tests\statics\StaticTestSuite;

class CheckAssertions extends StaticTestSuite {

    function assertSomething() {
        $this->assert(true);
        $this->assert("foo");
    }

    function assertingTheOpposite() {
        $this->assert->not("");
        $this->assert->not(null);

        $this->assert->not()->isTrue(false);
        $this->assert->not()->equals("foo", "bar");

        $this->shouldFail(function () {
            $this->assert->not(true);
        }, 'TRUE should not equal TRUE');

        $this->shouldFail(function () {
            $this->assert->not()->equals('a', 'a');
        }, "'a' should not equal 'a'");
    }

    function assertSomethingIsTrue() {
        $this->assert->isTrue(42 > 27);

        $this->shouldFail(function () {
            $this->assert->isTrue("not true");
        }, "'not true' should be TRUE");
    }

    function assertThingsAreEqual() {
        $this->assert->equals("foo", "foo");
        $this->assert->equals("1", 1.0);
        $this->assert->equals(new \DateTime(), new \DateTime());

        $this->shouldFail(function () {
            $this->assert->equals("foo", "bar");
        }, "'foo' should equal 'bar'");
    }

    function assertInstanceOfSomething() {
        $this->assert->isInstanceOf(new \DateTime(), \DateTime::class);
        $this->assert->isInstanceOf(new \DateTime(), \DateTime::class);
        $this->assert->isInstanceOf(new \DateTime(), \DateTimeInterface::class);

        $this->shouldFail(function () {
            /** @noinspection PhpParamsInspection */
            $this->assert->isInstanceOf("foo", \DateTime::class);
        }, "'foo' should be an object");

        $this->shouldFail(function () {
            $this->assert->isInstanceOf(new \StdClass(), \DateTime::class);
        }, '<stdClass> should be a <DateTime>');
    }

    function assertSomethingContainsSomething() {
        $this->assert->contains(["foo", "bar"], "foo");
        $this->assert->contains("foobar", "oob");
        $this->assert->contains(["foo", "bar"], "foo");

        $stack = new \SplStack();
        $stack->push("foo");
        $stack->push("bar");
        $this->assert->contains($stack, "bar");

        $object = new \StdClass;
        $object->property = "foo";
        $this->assert->contains($object, "foo");

        $this->shouldFail(function () {
            $this->assert->contains(true, "foo");
        }, "TRUE should contain 'foo'");
    }

    function assertSizeOfSomething() {
        $this->assert->size(["foo", 42, 27], 3);
        $this->assert->size(["foo", "bar"], 2);
        $this->assert->size("foo", 3);

        $stack = new \SplStack();
        $stack->push("foo");
        $stack->push("bar");
        $this->assert->size($stack, 2);

        $this->shouldFail(function () {
            $this->assert->size(true, 1);
        }, "TRUE should be countable");

        $this->shouldFail(function () {
            $this->assert->size("foo", 1);
        }, "'foo' should have length 1");

        $this->shouldFail(function () {
            $this->assert->size(["foo", "bar"], 3);
        }, "Counted size 2 should be 3");
    }

    function somethingIsNull() {
        $this->assert->isNull(null);

        $this->shouldFail(function () {
            $this->assert->isNull("not");
        }, "'not' should be NULL");
    }

    function printComplexValues() {
        $this->shouldFail(function () {
            $this->assert->isNull(["foo", "bar" => "baz"]);
        }, "[0: 'foo', 'bar': 'baz'] should be NULL");

        $this->shouldFail(function () {
            $this->assert->isNull(new \DateTime('2011-12-13 14:15:16 UTC'));
        }, "<DateTime>(2011-12-13T14:15:16+00:00) should be NULL");

        $this->shouldFail(function () {
            $stack = new \SplStack();
            $stack->push(["foo", "bar"]);
            $stack->push(new \DateTime('2011-12-13 14:15:16 UTC'));
            $this->assert->isNull($stack);
        }, "<SplStack>[<DateTime>(2011-12-13T14:15:16+00:00), ['foo', 'bar']] should be NULL");

        $this->shouldFail(function () {
            $this->assert->isNull(new TestName('some', 'name'));
        }, "<" . TestName::class . ">('some::name') should be NULL");
    }

    private function shouldFail(callable $do, $message) {
        try {
            $do();
            $this->fail("Should have failed");
        } catch (AssertionFailedFailure $f) {
            $this->assert($f->getFailureMessage(), $message);
        }
    }
}
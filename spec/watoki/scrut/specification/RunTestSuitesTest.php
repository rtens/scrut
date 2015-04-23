<?php
namespace spec\watoki\scrut\specification;

use watoki\scrut\FailedTestResult;
use watoki\scrut\PassedTestResult;
use watoki\scrut\ScrutinizeListener;
use watoki\scrut\Scrutinizer;
use watoki\scrut\TestResult;
use watoki\scrut\TestSuite;

class RunTestSuitesTest extends \PHPUnit_Framework_TestCase {

    /** @var RunPlainClassTest_Listener */
    private $listener;

    /** @var Scrutinizer */
    private $scrutinizer;

    protected function setUp() {
        $this->listener = new RunPlainClassTest_Listener();
        $this->scrutinizer = new Scrutinizer($this->listener);
        $this->scrutinizer->listen($this->listener);
    }

    public function testNoSuites() {
        $this->scrutinizer->run();
        $this->assertCount(0, $this->listener->started);
        $this->assertCount(0, $this->listener->finished);
    }

    public function testEmptySuite() {
        $this->scrutinizer->add(new RunPlainClass_Suite("Foo"));
        $this->scrutinizer->run();
        $this->assertCount(0, $this->listener->started);
        $this->assertCount(0, $this->listener->finished);
    }

    public function testSimpleSuite() {
        $this->scrutinizer->add(new RunPlainClass_Suite("Foo", [
            'bar' => function () {
            }
        ]));
        $this->scrutinizer->run();

        $this->assertCount(1, $this->listener->started);
        $this->assertCount(1, $this->listener->finished);
        $this->assertEquals("Foo::bar", $this->listener->started[0]);
        $this->assertEquals("Foo::bar", $this->listener->finished[0]);
    }

    public function testSecondListener() {
        $this->scrutinizer->add(new RunPlainClass_Suite("Foo", [
            'bar' => function () {
            }
        ]));
        $secondListener = new RunPlainClassTest_Listener();
        $this->scrutinizer->listen($secondListener);
        $this->scrutinizer->run();

        $this->assertCount(1, $this->listener->started);
        $this->assertCount(1, $secondListener->started);
    }

    public function testPassingTest() {
        $this->scrutinizer->add(new RunPlainClass_Suite("Foo", [
            'bar' => function () {
                // Passes
            }
        ]));
        $this->scrutinizer->run();

        $this->assertInstanceOf(PassedTestResult::class, $this->listener->results[0]);
    }

    public function testFailingTest() {
        $this->scrutinizer->add(new RunPlainClass_Suite("Foo", [
            'bar' => function () {
                throw new \Exception('Failed miserably');
            }
        ]));
        $this->scrutinizer->run();

        /** @var FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assertInstanceOf(FailedTestResult::class, $result);
        $this->assertEquals("Failed miserably", $result->exception()->getMessage());
    }
}

class RunPlainClassTest_Listener implements ScrutinizeListener {

    public $started = [];
    public $finished = [];
    public $results = [];

    public function started($name) {
        $this->started[] = $name;
    }

    public function finished($name, TestResult $result) {
        $this->finished[] = $name;
        $this->results[] = $result;
    }
}

class RunPlainClass_Suite extends TestSuite {

    private $name;
    private $tests = [];

    function __construct($name, $tests = []) {
        $this->name = $name;
        $this->tests = $tests;
    }

    public function run(ScrutinizeListener $listener) {
        foreach ($this->tests as $name => $callback) {
            $this->runTest($listener, $name, $callback);
        }
    }

    public function name() {
        return $this->name;
    }
}
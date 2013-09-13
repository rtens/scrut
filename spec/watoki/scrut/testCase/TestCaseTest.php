<?php
namespace spec\watoki\scrut\testCase;
 
use watoki\scrut\TestCase;

/**
 * @property TestCaseFixture testCase<-
 */
class TestCaseTest extends TestCase {

    public function testRunAllTests() {
        $this->testCase->givenTheClassDefinition('
            class RunAllTest extends \watoki\scrut\TestCase {
                function testFoo() {
                    spec\watoki\scrut\testCase\TestCaseTest::$run++;
                }
                function testBar() {
                    spec\watoki\scrut\testCase\TestCaseTest::$run++;
                }
            }
        ');

        $this->testCase->whenIRunTheTest('RunAllTest');

        $this->then_TestsShouldHaveRun(2);
    }

    public function testRunFailingTests() {
        $this->testCase->givenTheClassDefinition('
            class RunFailingTest extends \watoki\scrut\TestCase {
                function testFoo() {
                    $this->fail();
                    spec\watoki\scrut\testCase\TestCaseTest::$run++;
                }
                function testBar() {
                    spec\watoki\scrut\testCase\TestCaseTest::$run++;
                }
            }
        ');

        $this->testCase->whenIRunTheTest('RunFailingTest');

        $this->then_TestsShouldHaveRun(1);
        $this->testCase->thenTheResultShouldContain_FailedTest(1);
    }

    public function testUndos() {
        $this->testCase->givenTheClassDefinition('
            class UndoTest extends \watoki\scrut\TestCase {
                function testFooBar() {
                    $this->undos[] = function () {
                        spec\watoki\scrut\testCase\TestCaseTest::$run--;
                    };
                }
            }
        ');

        $this->testCase->whenIRunTheTest('UndoTest');

        $this->then_TestsShouldHaveRun(-1);
    }

    public static $run = 0;

    protected function setUp() {
        parent::setUp();
        self::$run = 0;
    }

    public function then_TestsShouldHaveRun($int) {
        $this->assertEquals($int, self::$run);
    }

}

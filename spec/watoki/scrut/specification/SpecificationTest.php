<?php
namespace spec\watoki\scrut\specification;
 
use watoki\scrut\Specification;

/**
 * @property SpecificationFixture $specification <-
 */
class SpecificationTest extends Specification {

    public function testRunAllTests() {
        $this->specification->givenTheClassDefinition('
            class RunAllTest extends \watoki\scrut\Specification {
                function testFoo() {
                    spec\watoki\scrut\specification\SpecificationTest::$run++;
                }
                function testBar() {
                    spec\watoki\scrut\specification\SpecificationTest::$run++;
                }
            }
        ');

        $this->specification->whenIRunTheTest('RunAllTest');

        $this->then_TestsShouldHaveRun(2);
    }

    public function testRunFailingTests() {
        $this->specification->givenTheClassDefinition('
            class RunFailingTest extends \watoki\scrut\Specification {
                function testFoo() {
                    $this->fail();
                    spec\watoki\scrut\specification\SpecificationTest::$run++;
                }
                function testBar() {
                    spec\watoki\scrut\specification\SpecificationTest::$run++;
                }
            }
        ');

        $this->specification->whenIRunTheTest('RunFailingTest');

        $this->then_TestsShouldHaveRun(1);
        $this->specification->thenTheResultShouldContain_FailedTest(1);
    }

    public function testUndos() {
        $this->specification->givenTheClassDefinition('
            class UndoTest extends \watoki\scrut\Specification {
                function testFooBar() {
                    $this->undos[] = function () {
                        spec\watoki\scrut\specification\SpecificationTest::$run--;
                    };
                }
            }
        ');

        $this->specification->whenIRunTheTest('UndoTest');

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

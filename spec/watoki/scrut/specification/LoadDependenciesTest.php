<?php
namespace spec\watoki\scrut\specification;

use watoki\scrut\Specification;

/**
 * @property SpecificationFixture $specification <-
 */
class LoadDependenciesTest extends Specification {

    public function testInjectPropertyAnnotations() {
        $this->specification->givenTheClass_InNamespace('SomeFixture', 'spec\watoki\scrut\tmp');
        $this->specification->givenTheClassDefinition('
            /**
             * @property spec\watoki\scrut\tmp\SomeFixture foo <-
             */
            class SomeTest extends \watoki\scrut\Specification {
                function runAllScenarios($prefix = "test") {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('SomeTest');

        $this->specification->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'spec\watoki\scrut\tmp\SomeFixture');
    }

    public function testReferenceToTest() {
        $this->specification->givenTheClassDefinition_InFile('
            class FixtureWithReference extends \watoki\scrut\Fixture {
                public function __construct(\watoki\scrut\Specification $spec, \watoki\factory\Factory $factory) {
                    spec\watoki\scrut\specification\LoadDependenciesTest::$testReference = get_class($spec);
                }
            }
        ', 'FixtureWithReference.php');

        $this->specification->givenTheClassDefinition('
            /**
             * @property FixtureWithReference foo <-
             */
            class TestReferenceTest extends \watoki\scrut\Specification {
                function runAllScenarios($prefix = "test") {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('TestReferenceTest');

        $this->then_ShouldBePassedToTheFixture('TestReferenceTest');
    }

    public function testLoadDependenciesOfFixture() {
        $this->specification->givenTheClassDefinition_InFile('
            class Dependency extends \watoki\scrut\Fixture {}
        ', 'Dependency.php');
        $this->specification->givenTheClassDefinition_InFile('
            /**
             * @property Dependency bar <-
             */
            class FixtureWithDependencies extends \watoki\scrut\Fixture {}
        ', 'FixtureWithDependencies.php');

        $this->specification->givenTheClassDefinition('
            /**
             * @property FixtureWithDependencies foo <-
             */
            class FixtureWithDependenciesTest extends \watoki\scrut\Specification {
                function runAllScenarios($prefix = "test") {
                    $this->setUp();
                    spec\watoki\scrut\specification\LoadDependenciesTest::$loaded[] = get_class($this->foo->bar);
                }
            }
        ');

        $this->specification->whenIRunTheTest('FixtureWithDependenciesTest');

        $this->then_FixturesShouldBeLoaded(1);
        $this->thenLoadedFixture_ShouldBe(1, 'Dependency');
    }

    public static $testReference;

    public static $loaded = array();

    protected function setUp() {
        parent::setUp();

        self::$loaded = array();
        self::$testReference = null;
    }

    private function then_FixturesShouldBeLoaded($int) {
        $this->assertCount($int, self::$loaded);
    }

    private function thenLoadedFixture_ShouldBe($int, $class) {
        $this->assertEquals($class, self::$loaded[$int - 1]);
    }

    private function then_ShouldBePassedToTheFixture($string) {
        $this->assertEquals($string, self::$testReference);
    }

}
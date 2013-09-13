<?php
namespace spec\watoki\scrut\specification;

use watoki\scrut\Specification;

/**
 * @property SpecificationFixture $specification <-
 */
class LoadDependenciesTest extends Specification {

    public function testFullyQualifiedClassNames() {
        $this->specification->givenTheClass_InNamespace('SomeFixture', 'spec\watoki\scrut\tmp');
        $this->specification->givenTheClassDefinition('
            /**
             * @property spec\watoki\scrut\tmp\SomeFixture foo <-
             */
            class SomeTest extends \watoki\scrut\Specification {
                function runAllScenarios() {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('SomeTest');

        $this->specification->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'spec\watoki\scrut\tmp\SomeFixture');
    }

    public function testRelativeNamespace() {
        $this->specification->givenTheClass_InNamespace('AnotherFixture', 'spec\watoki\scrut\tmp\inside');
        $this->specification->givenTheClassDefinition('
            namespace spec\watoki\scrut\tmp;

            /**
             * @property inside\AnotherFixture foo <-
             */
            class RelativeTest extends \watoki\scrut\Specification {
                function runAllScenarios() {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('spec\watoki\scrut\tmp\RelativeTest');

        $this->specification->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'spec\watoki\scrut\tmp\inside\AnotherFixture');
    }

    public function testClassAliases() {
        $this->specification->givenTheClass_InNamespace('AliasedFixture', 'spec\watoki\scrut\tmp');
        $this->specification->givenTheClassDefinition('
            use spec\watoki\scrut\tmp\AliasedFixture;

            /**
             * @property AliasedFixture foo <-
             */
            class AliasTest extends \watoki\scrut\Specification {
                function runAllScenarios() {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('AliasTest');

        $this->specification->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'spec\watoki\scrut\tmp\AliasedFixture');
    }

    public function testDontInjectNotMarkedProperties() {
        $this->specification->givenTheClassDefinition_InFile('
            class JustSomeFixture extends \watoki\scrut\Fixture {}
        ', 'JustSomeFixture.php');
        $this->specification->givenTheClassDefinition('
            /**
             * @property JustSomeFixture foo
             * @property JustSomeFixture bar <-
             */
            class NotAllTest extends \watoki\scrut\Specification {
                function runAllScenarios() {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('NotAllTest');

        $this->specification->thenItShouldHaveAProperty_WithAnInstanceOf('bar', 'JustSomeFixture');
        $this->specification->thenItShouldNoHaveAProperty('foo');
    }

    public function testInjectPropertyWithDollarSign() {
        $this->specification->givenTheClassDefinition_InFile('
            class JustAnotherFixture extends \watoki\scrut\Fixture {}
        ', 'JustAnotherFixture.php');
        $this->specification->givenTheClassDefinition('
            /**
             * @property JustAnotherFixture $foo <-
             */
            class DollarSignTest extends \watoki\scrut\Specification {
                function runAllScenarios() {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('DollarSignTest');

        $this->specification->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'JustAnotherFixture');
    }

    public function testInjectProtectedProperty() {
        $this->specification->givenTheClassDefinition_InFile('
            class ProtectedFixture extends \watoki\scrut\Fixture {}
        ', 'ProtectedFixture.php');
        $this->specification->givenTheClassDefinition('
            /**
             * @property ProtectedFixture foo <-
             */
            class ProtectedPropertyTest extends \watoki\scrut\Specification {
                protected $foo;
                function runAllScenarios() {
                    $this->setUp();
                    spec\watoki\scrut\specification\LoadDependenciesTest::$loaded[] = get_class($this->foo);
                }
            }
        ');

        $this->specification->whenIRunTheTest('ProtectedPropertyTest');

        $this->then_FixturesShouldBeLoaded(1);
        $this->thenLoadedFixture_ShouldBe(1, 'ProtectedFixture');
    }

    public function testOrder() {
        $this->specification->givenTheClassDefinition_InFile('
            class FirstFixture extends \watoki\scrut\Fixture {
                public function __construct(\watoki\scrut\Specification $spec, \watoki\factory\Factory $factory) {
                    spec\watoki\scrut\specification\LoadDependenciesTest::$loaded[] = get_class($this);
                }
            }
        ', 'FirstFixture.php');
        $this->specification->givenTheClassDefinition_InFile('
            class SecondFixture extends \watoki\scrut\Fixture {
                public function __construct(\watoki\scrut\Specification $spec, \watoki\factory\Factory $factory) {
                    spec\watoki\scrut\specification\LoadDependenciesTest::$loaded[] = get_class($this);
                }
            }
        ', 'SecondFixture.php');

        $this->specification->givenTheClassDefinition('
            /**
             * @property FirstFixture foo1 <-
             * @property SecondFixture foo2 <-
             */
            class OrderTest extends \watoki\scrut\Specification {
                function runAllScenarios() {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('OrderTest');

        $this->then_FixturesShouldBeLoaded(2);
        $this->thenLoadedFixture_ShouldBe(1, 'FirstFixture');
        $this->thenLoadedFixture_ShouldBe(2, 'SecondFixture');
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
                function runAllScenarios() {
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
             * @property Dependency foo <-
             */
            class FixtureWithDependencies extends \watoki\scrut\Fixture {
                public function __construct(\watoki\scrut\Specification $spec, \watoki\factory\Factory $factory) {
                    parent::__construct($spec, $factory);
                    spec\watoki\scrut\specification\LoadDependenciesTest::$loaded[] = get_class($this->foo);
                    spec\watoki\scrut\specification\LoadDependenciesTest::$loaded[] = get_class($this);
                }
            }
        ', 'FixtureWithDependencies.php');

        $this->specification->givenTheClassDefinition('
            /**
             * @property FixtureWithDependencies foo <-
             */
            class FixtureWithDependenciesTest extends \watoki\scrut\Specification {
                function runAllScenarios() {
                    $this->setUp();
                }
            }
        ');

        $this->specification->whenIRunTheTest('FixtureWithDependenciesTest');

        $this->then_FixturesShouldBeLoaded(2);
        $this->thenLoadedFixture_ShouldBe(1, 'Dependency');
        $this->thenLoadedFixture_ShouldBe(2, 'FixtureWithDependencies');
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
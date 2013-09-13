<?php
namespace spec\watoki\scrut\testCase;

use watoki\scrut\TestCase;

/**
 * @property TestCaseFixture testCase<-
 */
class LoadDependenciesTest extends TestCase {

    public function testFullyQualifiedClassNames() {
        $this->testCase->givenTheClass_InNamespace('SomeFixture', 'spec\watoki\scrut\tmp');
        $this->testCase->givenTheClassDefinition('
            /**
             * @property spec\watoki\scrut\tmp\SomeFixture foo<-
             */
            class SomeTest extends \watoki\scrut\TestCase {
                function runAllTests() {
                    $this->setUp();
                }
            }
        ');

        $this->testCase->whenIRunTheTest('SomeTest');

        $this->testCase->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'spec\watoki\scrut\tmp\SomeFixture');
    }

    public function testRelativeNamespace() {
        $this->testCase->givenTheClass_InNamespace('AnotherFixture', 'spec\watoki\scrut\tmp\inside');
        $this->testCase->givenTheClassDefinition('
            namespace spec\watoki\scrut\tmp;

            /**
             * @property inside\AnotherFixture foo<-
             */
            class RelativeTest extends \watoki\scrut\TestCase {
                function runAllTests() {
                    $this->setUp();
                }
            }
        ');

        $this->testCase->whenIRunTheTest('spec\watoki\scrut\tmp\RelativeTest');

        $this->testCase->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'spec\watoki\scrut\tmp\inside\AnotherFixture');
    }

    public function testClassAliases() {
        $this->testCase->givenTheClass_InNamespace('AliasedFixture', 'spec\watoki\scrut\tmp');
        $this->testCase->givenTheClassDefinition('
            use spec\watoki\scrut\tmp\AliasedFixture;

            /**
             * @property AliasedFixture foo<-
             */
            class AliasTest extends \watoki\scrut\TestCase {
                function runAllTests() {
                    $this->setUp();
                }
            }
        ');

        $this->testCase->whenIRunTheTest('AliasTest');

        $this->testCase->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'spec\watoki\scrut\tmp\AliasedFixture');
    }

    public function testDontInjectNotMarkedProperties() {
        $this->testCase->givenTheClassDefinition_InFile('
            class JustSomeFixture extends \watoki\scrut\Fixture {}
        ', 'JustSomeFixture.php');
        $this->testCase->givenTheClassDefinition('
            /**
             * @property JustSomeFixture foo
             * @property JustSomeFixture bar<-
             */
            class NotAllTest extends \watoki\scrut\TestCase {
                function runAllTests() {
                    $this->setUp();
                }
            }
        ');

        $this->testCase->whenIRunTheTest('NotAllTest');

        $this->testCase->thenItShouldHaveAProperty_WithAnInstanceOf('bar', 'JustSomeFixture');
        $this->testCase->thenItShouldNoHaveAProperty('foo');
    }

    public function testInjectPropertyWithDollarSign() {
        $this->testCase->givenTheClassDefinition_InFile('
            class JustAnotherFixture extends \watoki\scrut\Fixture {}
        ', 'JustAnotherFixture.php');
        $this->testCase->givenTheClassDefinition('
            /**
             * @property JustAnotherFixture $foo<-
             */
            class DollarSignTest extends \watoki\scrut\TestCase {
                function runAllTests() {
                    $this->setUp();
                }
            }
        ');

        $this->testCase->whenIRunTheTest('DollarSignTest');

        $this->testCase->thenItShouldHaveAProperty_WithAnInstanceOf('foo', 'JustAnotherFixture');
    }

    public function testOrder() {
        $this->testCase->givenTheClassDefinition_InFile('
            class FirstFixture extends \watoki\scrut\Fixture {
                public function __construct(\watoki\scrut\TestCase $test, \watoki\factory\Factory $factory) {
                    spec\watoki\scrut\testCase\LoadDependenciesTest::$loaded[] = get_class($this);
                }
            }
        ', 'FirstFixture.php');
        $this->testCase->givenTheClassDefinition_InFile('
            class SecondFixture extends \watoki\scrut\Fixture {
                public function __construct(\watoki\scrut\TestCase $test, \watoki\factory\Factory $factory) {
                    spec\watoki\scrut\testCase\LoadDependenciesTest::$loaded[] = get_class($this);
                }
            }
        ', 'SecondFixture.php');

        $this->testCase->givenTheClassDefinition('
            /**
             * @property FirstFixture foo1<-
             * @property SecondFixture foo2<-
             */
            class OrderTest extends \watoki\scrut\TestCase {
                function runAllTests() {
                    $this->setUp();
                }
            }
        ');

        $this->testCase->whenIRunTheTest('OrderTest');

        $this->then_FixturesShouldBeLoaded(2);
        $this->thenLoadedFixture_ShouldBe(1, 'FirstFixture');
        $this->thenLoadedFixture_ShouldBe(2, 'SecondFixture');
    }

    public function testReferenceToTest() {
        $this->testCase->givenTheClassDefinition_InFile('
            class FixtureWithReference extends \watoki\scrut\Fixture {
                public function __construct(\watoki\scrut\TestCase $test, \watoki\factory\Factory $factory) {
                    spec\watoki\scrut\testCase\LoadDependenciesTest::$testReference = get_class($test);
                }
            }
        ', 'FixtureWithReference.php');

        $this->testCase->givenTheClassDefinition('
            /**
             * @property FixtureWithReference foo<-
             */
            class TestReferenceTest extends \watoki\scrut\TestCase {
                function runAllTests() {
                    $this->setUp();
                }
            }
        ');

        $this->testCase->whenIRunTheTest('TestReferenceTest');

        $this->then_ShouldBePassedToTheFixture('TestReferenceTest');
    }

    public function testLoadDependenciesOfFixture() {
        $this->testCase->givenTheClassDefinition_InFile('
            class Dependency extends \watoki\scrut\Fixture {}
        ', 'Dependency.php');
        $this->testCase->givenTheClassDefinition_InFile('
            /**
             * @property Dependency foo<-
             */
            class FixtureWithDependencies extends \watoki\scrut\Fixture {
                public function __construct(\watoki\scrut\TestCase $test, \watoki\factory\Factory $factory) {
                    parent::__construct($test, $factory);
                    spec\watoki\scrut\testCase\LoadDependenciesTest::$loaded[] = get_class($this->foo);
                    spec\watoki\scrut\testCase\LoadDependenciesTest::$loaded[] = get_class($this);
                }
            }
        ', 'FixtureWithDependencies.php');

        $this->testCase->givenTheClassDefinition('
            /**
             * @property FixtureWithDependencies foo<-
             */
            class FixtureWithDependenciesTest extends \watoki\scrut\TestCase {
                function runAllTests() {
                    $this->setUp();
                }
            }
        ');

        $this->testCase->whenIRunTheTest('FixtureWithDependenciesTest');

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
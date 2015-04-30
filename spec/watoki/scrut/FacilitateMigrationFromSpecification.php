<?php
namespace watoki\scrut;

use watoki\factory\Factory;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\tests\migration\Specification;
use watoki\scrut\tests\statics\StaticTestSuite;

class FacilitateMigrationFromSpecification extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
    }

    function callBackgroundHook() {
        FacilitateMigrationFromSpecification_Foo::$backgroundCalled = false;

        $suite = new FacilitateMigrationFromSpecification_Foo(new Factory());
        $suite->run($this->listener);

        $this->assert(FacilitateMigrationFromSpecification_Foo::$backgroundCalled);
    }

    function undoStuff() {
        FacilitateMigrationFromSpecification_Undo::$undid = false;

        $suite = new FacilitateMigrationFromSpecification_Undo(new Factory());
        $suite->run($this->listener);

        $this->assert(FacilitateMigrationFromSpecification_Undo::$undid);
    }

    function passSpecificationToInjectedDependencies() {
        $suite = new FacilitateMigrationFromSpecification_Inject(new Factory());
        $suite->run($this->listener);

        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }
}

class FacilitateMigrationFromSpecification_Foo extends Specification {

    public static $backgroundCalled;

    protected function background() {
        self::$backgroundCalled = true;
    }

    function testFoo() {
        $this->assert(self::$backgroundCalled);
    }
}

class FacilitateMigrationFromSpecification_Undo extends Specification {

    public static $undid;

    function testFoo() {
        $this->undos[] = function () {
            self::$undid = true;
        };
    }
}

class FacilitateMigrationFromSpecification_Inject extends Specification {

    /** @var FacilitateMigrationFromSpecification_Dependency <- */
    protected $foo;

    function testFoo() {
        $this->assert($this === $this->foo->spec());
    }
}

class FacilitateMigrationFromSpecification_Dependency extends \watoki\scrut\tests\migration\Fixture {

    function spec() {
        return $this->spec;
    }
}
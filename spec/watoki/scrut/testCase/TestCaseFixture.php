<?php
namespace spec\watoki\scrut\testCase;
 
use watoki\scrut\TestCase;
use watoki\scrut\Fixture;

class TestCaseFixture extends Fixture {

    /** @var \PHPUnit_TestResult */
    private $result;

    /** @var TestCase */
    private $instance;

    public function givenTheClass_InNamespace($class, $namespace) {
        $this->givenTheClassDefinition_InFile("
        namespace $namespace;

        class $class {}
        ", $class . '.php');
    }

    public function givenTheClassDefinition_InFile($code, $fileName) {
        $dir = __DIR__ . '/tmp';
        $file = $dir . '/' . $fileName;

        @mkdir($dir);
        file_put_contents($file, "<?php $code");

        $this->test->undos[] = function () use ($file) {
            @unlink($file);
        };
        $this->test->undos[] = function () use ($dir) {
            @rmdir($dir);
        };

        include $file;
    }

    public function givenTheClassDefinition($code) {
        $this->givenTheClassDefinition_InFile($code, 'class.php');
    }

    public function whenIRunTheTest($className) {
        $this->instance = new $className;
        $this->result = $this->instance->runAllTests();
    }

    public function thenItShouldHaveAProperty_WithAnInstanceOf($propertyName, $className) {
        $this->thenItShouldHaveAProperty($propertyName);
        $this->thenItsProperty_ShouldBeAnInstanceOf($propertyName, $className);
    }

    private function thenItShouldHaveAProperty($propertyName) {
        $this->instance->assertTrue(property_exists($this->instance, $propertyName), "[$propertyName] not found.");
    }

    private function thenItsProperty_ShouldBeAnInstanceOf($propertyName, $className) {
        $this->instance->assertInstanceOf($className, $this->instance->$propertyName);
    }

    public function thenTheResultShouldContain_FailedTest($int) {
        $this->test->assertEquals($int, $this->result->failureCount());
    }

    public function thenItShouldNoHaveAProperty($property) {
        $this->instance->assertFalse(isset($this->instance->$property));
    }

}

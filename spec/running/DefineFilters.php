<?php
namespace rtens\scrut\running;

use rtens\scrut\Assert;
use rtens\scrut\TestName;
use rtens\scrut\tests\TestFilter;
use watoki\factory\Factory;

/**
 * The TestFilter can be configured to filter files, classes and methods
 *
 * @property Assert assert <-
 */
class DefineFilters {

    function defaultFilter() {
        $this->whenIExecuteTheCommand();
        $this->thenTheFilterShouldAcceptTheClass(DefineFilters_FooSpec::class);
        $this->thenTheFilterShouldAcceptTheFile('any/file');
        $this->thenTheFilterShouldAcceptTheMethod(self::class, 'defaultFilter');
    }

    function shortDefinition() {
        $this->givenTheConfiguration([
            'filter' => '/.+Time/'
        ]);
        $this->whenIExecuteTheCommand();

        $this->thenTheFilterShouldNotAcceptTheClass(DefineFilters::class);
        $this->thenTheFilterShouldAcceptTheClass(\DateTime::class);
        $this->thenTheFilterShouldAcceptTheClass(\DateTimeImmutable::class);
    }

    function filterClassesByNameShort() {
        $this->givenTheConfiguration([
            'filter' => [
                'class' => '/.+Time$/'
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->thenTheFilterShouldAcceptTheClass(\DateTime::class);
        $this->thenTheFilterShouldNotAcceptTheClass(\DateTimeImmutable::class);
    }

    function filterClassesByNameExplicit() {
        $this->givenTheConfiguration([
            'filter' => [
                'class' => [
                    'name' => '/.+Time$/'
                ]
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->thenTheFilterShouldAcceptTheClass(\DateTime::class);
        $this->thenTheFilterShouldNotAcceptTheClass(\DateTimeImmutable::class);
    }

    function filterFilesByName() {
        $this->givenTheConfiguration([
            'filter' => [
                'file' => '/.+Test$/'
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->thenTheFilterShouldAcceptTheFile('foo/MyTest');
        $this->thenTheFilterShouldNotAcceptTheFile('foo/MyTestNot');
    }

    function filterMethodsByNameShort() {
        $this->givenTheConfiguration([
            'filter' => [
                'method' => '/filter.+/'
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->thenTheFilterShouldAcceptTheMethod(self::class, 'filterMethodsByNameShort');
        $this->thenTheFilterShouldNotAcceptTheMethod(self::class, 'defaultFilter');
    }

    function filterMethodsByNameExplicit() {
        $this->givenTheConfiguration([
            'filter' => [
                'method' => [
                    'name' => '/filter.+/'
                ]
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->thenTheFilterShouldAcceptTheMethod(self::class, 'filterMethodsByNameExplicit');
        $this->thenTheFilterShouldNotAcceptTheMethod(self::class, 'defaultFilter');
    }

    function filterClassBySubclass() {
        $this->givenTheConfiguration([
            'filter' => [
                'class' => [
                    'subclass' => \DateTimeInterface::class
                ]
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->thenTheFilterShouldNotAcceptTheClass(self::class);
        $this->thenTheFilterShouldAcceptTheClass(\DateTime::class);
        $this->thenTheFilterShouldAcceptTheClass(\DateTimeImmutable::class);
    }

    /**
     * @test me
     */
    function filterMethodsByAnnotations() {
        $this->givenTheConfiguration([
            'filter' => [
                'method' => [
                    'annotation' => '/@test .+/'
                ]
            ]
        ]);
        $this->whenIExecuteTheCommand();

        $this->thenTheFilterShouldAcceptTheMethod(self::class, __FUNCTION__);
        $this->thenTheFilterShouldNotAcceptTheMethod(self::class, 'defaultFilter');
    }

    /** @var TestFilter */
    private $filter;

    private $config = [];

    private function givenTheConfiguration(array $config) {
        $this->config = $config;
    }

    private function whenIExecuteTheCommand() {
        $command = new ScrutCommand(new ConfigurationReader('foo', new Factory()));
        $command->execute(['-c' . json_encode(array_merge(
            $this->config,
            ["runner" => DefineFilters_TestRunner::class]
        ))]);
        $this->filter = DefineFilters_TestRunner::$config->getFilter();
    }

    private function thenTheFilterShouldAcceptTheClass($class) {
        $this->assert->isTrue($this->filter->acceptsClass(new \ReflectionClass($class)));
    }

    private function thenTheFilterShouldAcceptTheFile($path) {
        $this->assert->isTrue($this->filter->acceptsFile($path));
    }

    private function thenTheFilterShouldNotAcceptTheFile($path) {
        $this->assert->not()->isTrue($this->filter->acceptsFile($path));
    }

    private function thenTheFilterShouldAcceptTheMethod($class, $methodName) {
        $this->assert->isTrue($this->filter->acceptsMethod(new \ReflectionMethod($class, $methodName)));
    }

    private function thenTheFilterShouldNotAcceptTheMethod($class, $methodName) {
        $this->assert->not()->isTrue($this->filter->acceptsMethod(new \ReflectionMethod($class, $methodName)));
    }

    private function thenTheFilterShouldNotAcceptTheClass($class) {
        $this->assert->not()->isTrue($this->filter->acceptsClass(new \ReflectionClass($class)));
    }
}

class DefineFilters_TestRunner extends TestRunner {

    /** @var TestRunConfiguration */
    public static $config;

    /**
     * @param TestRunConfiguration $configuration <-
     */
    function __construct(TestRunConfiguration $configuration) {
        parent::__construct($configuration);
        self::$config = $configuration;
    }

    public function run(TestName $name = null) {
        return true;
    }

}

class DefineFilters_FooSpec {

}
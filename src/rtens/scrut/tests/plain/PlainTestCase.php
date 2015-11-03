<?php
namespace rtens\scrut\tests\plain;

use rtens\scrut\Fixture;
use watoki\factory\Factory;
use watoki\factory\Injector;
use watoki\factory\providers\CallbackProvider;
use rtens\scrut\Assert;
use rtens\scrut\TestName;
use rtens\scrut\tests\TestCase;
use watoki\factory\providers\DefaultProvider;

class PlainTestCase extends TestCase {

    const BEFORE_METHOD = 'before';
    const AFTER_METHOD = 'after';

    private static $ASSERTER_ARGUMENTS = ['assert', 'asserter'];

    private $asserterProvided = false;

    /** @var \ReflectionMethod */
    protected $method;

    /** @var array|Fixture[] */
    protected $providedFixtures = [];

    /**
     * @param \ReflectionMethod $method
     * @param TestName $parent
     */
    function __construct(\ReflectionMethod $method, TestName $parent = null) {
        parent::__construct($parent);
        $this->method = $method;
    }

    /**
     * @return TestName
     */
    public function getName() {
        return parent::getName()->with($this->method->getName());
    }

    /**
     * @return \rtens\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new PlainFailureSourceLocator($this->method);
    }

    protected function execute(Assert $assert) {
        $factory = $this->createFactory($assert);
        $suite = $factory->getInstance($this->method->getDeclaringClass()->getName());

        $this->callFixtureHook(self::BEFORE_METHOD);
        $this->callHook(self::BEFORE_METHOD, $suite, $factory);

        $args = $this->injectArguments($this->method, $factory, $this->injectAsserter($this->method, $assert));

        try {
            $this->method->invokeArgs($suite, $args);

            if (!$this->asserterProvided && !$this->isMethodEmpty()) {
                $assert->pass();
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->callHook(self::AFTER_METHOD, $suite, $factory);
            $this->callFixtureHook(self::AFTER_METHOD);
        }
    }

    protected function createFactory(Assert $assert) {
        $factory = new Factory();
        $provider = new DefaultProvider($factory);

        $factory->setProvider(Assert::class, new CallbackProvider(function () use ($assert) {
            $this->asserterProvided = true;
            return $assert;
        }));

        $factory->setProvider(Fixture::class, new CallbackProvider(function ($class, $args) use ($provider) {
            $instance = $provider->provide($class, $args);
            $this->providedFixtures[] = $instance;
            return $instance;
        }));

        return $factory;
    }

    private function callHook($methodName, $suite, Factory $factory) {
        if (method_exists($suite, $methodName)) {
            if (!is_callable([$suite, $methodName])) {
                $name = get_class($suite) . '::' . $methodName;
                throw new \ReflectionException("Method [" . $name . '] must be public');
            }

            $method = new \ReflectionMethod($suite, $methodName);
            $method->invokeArgs($suite, $this->injectArguments($method, $factory));
        }
    }

    private function injectArguments(\ReflectionMethod $method, Factory $factory, array $args = []) {
        $injector = new Injector($factory);
        return $injector->injectMethodArguments($method, $args, function () {
            return true;
        });
    }

    private function injectAsserter(\ReflectionMethod $method, Assert $assert) {
        $args = [];
        foreach ($method->getParameters() as $parameter) {
            if (in_array($parameter->getName(), self::$ASSERTER_ARGUMENTS)) {
                $this->asserterProvided = true;
                $args[$parameter->getName()] = $assert;
            }
        }
        return $args;
    }

    protected function callFixtureHook($methodName) {
        foreach ($this->providedFixtures as $fixture) {
            $fixture->$methodName();
        }
    }

    private function isMethodEmpty() {
        return $this->method->getEndLine() <= $this->method->getStartLine() + 1;
    }
}
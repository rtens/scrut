<?php
namespace rtens\scrut\tests\plain;

use watoki\factory\Factory;
use watoki\factory\Injector;
use watoki\factory\providers\CallbackProvider;
use rtens\scrut\Assert;
use rtens\scrut\TestName;
use rtens\scrut\tests\TestCase;

class PlainTestCase extends TestCase {

    const BEFORE_METHOD = 'before';
    const AFTER_METHOD = 'after';

    private static $ASSERTER_ARGUMENTS = ['assert', 'asserter'];

    private $asserterProvided = false;

    /** @var \ReflectionMethod */
    protected $method;

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

        $this->callHook($factory, $suite, self::BEFORE_METHOD);

        $args = $this->injectArguments($this->method, $factory, $this->injectAsserter($this->method, $assert));

        try {
            $this->method->invokeArgs($suite, $args);

            if (!$this->asserterProvided) {
                $assert->pass();
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->callHook($factory, $suite, self::AFTER_METHOD);
        }
    }

    private function createFactory(Assert $assert) {
        $factory = new Factory();
        $factory->setProvider(Assert::class, new CallbackProvider(function () use ($assert) {
            $this->asserterProvided = true;
            return $assert;
        }));
        return $factory;
    }

    private function callHook(Factory $factory, $suite, $methodName) {
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
}
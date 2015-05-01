<?php
namespace watoki\scrut\tests\plain;

use watoki\factory\Factory;
use watoki\factory\Injector;
use watoki\factory\providers\CallbackProvider;
use watoki\scrut\Asserter;
use watoki\scrut\TestName;
use watoki\scrut\tests\TestCase;

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

    protected function execute(Asserter $assert) {
        $class = $this->method->getDeclaringClass();

        $factory = $this->createFactory($assert);
        $suite = $factory->getInstance($class->getName());
        $args = $this->injectArguments($assert, $factory);

        $this->callHook($suite, self::BEFORE_METHOD);
        try {
            $this->method->invokeArgs($suite, $args);

            if (!$this->asserterProvided) {
                $assert->pass();
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->callHook($suite, self::AFTER_METHOD);
        }
    }

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new PlainFailureSourceLocator($this->method);
    }

    private function callHook($suite, $methodName) {
        if (method_exists($suite, $methodName)) {
            if (!is_callable([$suite, $methodName])) {
                $name = get_class($suite) . '::' . $methodName;
                throw new \ReflectionException("Method [" . $name . '] must be public');
            }
            $suite->$methodName();
        }
    }

    private function createFactory(Asserter $assert) {
        $factory = new Factory();
        $factory->setProvider(Asserter::class, new CallbackProvider(function () use ($assert) {
            $this->asserterProvided = true;
            return $assert;
        }));
        return $factory;
    }

    private function injectArguments(Asserter $assert, Factory $factory) {
        $args = [];

        foreach ($this->method->getParameters() as $parameter) {
            if (in_array($parameter->getName(), self::$ASSERTER_ARGUMENTS)) {
                $this->asserterProvided = true;
                $args[$parameter->getName()] = $assert;
            }
        }

        $injector = new Injector($factory);
        $args = $injector->injectMethodArguments($this->method, $args, function () {
            return true;
        });

        return $args;
    }
}
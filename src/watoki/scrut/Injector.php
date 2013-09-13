<?php
namespace watoki\scrut;
 
use rtens\mockster\ClassResolver;

class Injector {

    const MARKER = ' <-';

    private $target;

    public function __construct($target) {
        $this->target = $target;
    }

    /**
     * @param callable $factory
     * @param string $marker Suffix that needs to be appended after the annotation to be injected (e.g. @property MyClass foo<-
     * @throws \Exception
     */
    public function injectAnnotatedProperties($factory, $marker = self::MARKER) {
        $classReflection = new \ReflectionClass($this->target);
        $resolver = new ClassResolver($classReflection);

        $matches = array();
        preg_match_all('/@property (\S+) \$?(\S+)' . $marker .'/', $classReflection->getDocComment(), $matches);

        foreach ($matches[0] as $i => $match) {
            $className = $matches[1][$i];
            $property = $matches[2][$i];

            $class = $resolver->resolve($className);

            if (!$class) {
                throw new \Exception("Error while loading dependency [$property] of [{$classReflection->getShortName()}]: Could not find class [$className].");
            }

            if ($classReflection->hasProperty($property)) {
                $reflectionProperty = $classReflection->getProperty($property);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($this->target, $factory($class));
            } else {
                $this->target->$property = $factory($class);
            }
        }
    }
}

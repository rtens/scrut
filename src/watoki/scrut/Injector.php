<?php
namespace watoki\scrut;
 
use rtens\mockster\ClassResolver;

class Injector {

    private $target;

    public function __construct($target) {
        $this->target = $target;
    }

    /**
     * @param callable $factory
     * @throws \Exception
     */
    public function injectProperties($factory) {
        $refl = new \ReflectionClass($this->target);
        $resolver = new ClassResolver($refl);

        $matches = array();
        preg_match_all('/@property (\S+) (\S+)/', $refl->getDocComment(), $matches);

        foreach ($matches[0] as $i => $match) {
            $className = $matches[1][$i];
            $property = $matches[2][$i];

            $class = $resolver->resolve($className);

            if (!$class) {
                throw new \Exception("Error while loading dependency [$property] of [{$refl->getShortName()}]: Could not find class [$className].");
            }

            $this->target->$property = $factory($class);
        }
    }
}

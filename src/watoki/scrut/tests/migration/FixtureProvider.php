<?php
namespace watoki\scrut\tests\migration;

use watoki\factory\Factory;
use watoki\factory\providers\DefaultProvider;

class FixtureProvider extends DefaultProvider {

    private $spec;
    private $factory;

    function __construct(Specification $spec, Factory $factory) {
        parent::__construct($factory, true);
        $this->factory = $factory;
        $this->spec = $spec;
    }

    public function provide($class, array $args = []) {
        $instance = parent::provide($class, ['spec' => $this->spec]);
        $this->factory->setSingleton($class, $instance);
        return $instance;
    }
}
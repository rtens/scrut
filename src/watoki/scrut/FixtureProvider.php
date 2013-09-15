<?php
namespace watoki\scrut;

use watoki\factory\Injector;
use watoki\factory\providers\DefaultProvider;
use watoki\factory\providers\PropertyInjectionProvider;

class FixtureProvider extends PropertyInjectionProvider {

    private $spec;

    function __construct(Specification $spec) {
        parent::__construct($spec->factory, true);
        $this->spec = $spec;
    }

    public function provide($class, array $args = array()) {
        $instance = parent::provide($class, array('spec' => $this->spec));
        $this->spec->factory->setSingleton($class, $instance);
        return $instance;
    }
}
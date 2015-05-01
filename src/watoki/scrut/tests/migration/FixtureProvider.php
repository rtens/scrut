<?php
namespace watoki\scrut\tests\migration;

use watoki\factory\Factory;
use watoki\factory\providers\DefaultProvider;

class FixtureProvider extends DefaultProvider {

    private $spec;
    private $factory;

    /** @var Fixture[] */
    private $providedFixtures = [];

    function __construct(Specification $spec, Factory $factory) {
        parent::__construct($factory, true);
        $this->factory = $factory;
        $this->spec = $spec;
    }

    public function provide($class, array $args = []) {
        $instance = parent::provide($class, ['spec' => $this->spec]);
        $this->providedFixtures[] = $instance;
        $this->factory->setSingleton($class, $instance);
        return $instance;
    }

    public function getProvidedFixtures() {
        return $this->providedFixtures;
    }
}
<?php
namespace watoki\scrut;

use watoki\factory\Factory;
use watoki\factory\providers\DefaultProvider;

class FixtureProvider extends DefaultProvider {

    private $spec;

    private $factory;

    /** @var array|Fixture[] */
    private $providedFixtures = array();

    function __construct(Specification $spec, Factory $factory) {
        parent::__construct($factory, true);
        $this->factory = $factory;
        $this->spec = $spec;
    }

    public function provide($class, array $args = array()) {
        $instance = parent::provide($class, array('spec' => $this->spec));
        $this->factory->setSingleton($class, $instance);

        $this->providedFixtures[] = $instance;

        return $instance;
    }

    /**
     * @return array|\watoki\scrut\Fixture[]
     */
    public function getProvidedFixtures() {
        return $this->providedFixtures;
    }
}
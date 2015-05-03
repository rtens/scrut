<?php
namespace rtens\scrut\running;

use rtens\scrut\TestName;

class ScrutCommand {

    /** @var ConfigurationReader */
    private $reader;

    function __construct(ConfigurationReader $reader) {
        $this->reader = $reader;
    }

    /**
     * @param string[] $arguments The command arguments (without the name of the script itself)
     * @throws \Exception
     * @return int The exit value
     */
    public function execute(array $arguments) {
        $configFile = null;
        $runConfig = [];
        $name = null;

        foreach ($arguments as $a) {
            $key = substr($a, 0, 2);
            $value = substr($a, 2);

            if ($key == '-l') {
                $runConfig['listen'][] = $value;
            } else if ($key == '-c') {
                $argConfig = json_decode($value, true);
                if ($argConfig) {
                    $runConfig = array_merge_recursive($runConfig, $argConfig);
                } else {
                    $configFile = $value;
                }
            } else {
                $name = TestName::parse($a);
            }
        }

        $configuration = $this->reader->read($configFile, $runConfig);
        $runner = $configuration->getRunner();

        return $runner->run($name) ? 0 : 1;
    }
}
<?php
namespace rtens\scrut\cli;

use rtens\scrut\TestName;

class ScrutCommand {

    private static $configFileNames = [
        'scrut.json',
        'scrut.json.dist'
    ];

    /** @var string */
    protected $cwd;

    /** @var array|string[] */
    protected $argv;

    /**
     * @param string $cwd
     * @param array|string[] $argv
     */
    public function __construct($cwd, array $argv) {
        $this->cwd = $cwd;
        $this->argv = $argv;
    }

    public function execute() {
        $config = $this->getConfiguration();

        $name = null;
        if (count($this->argv) > 1) {
            $name = TestName::parse($this->argv[1]);
        }

        $runner = $this->createTestRunner($config);
        return $runner->run($name) ? 0 : 1;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getConfiguration() {
        foreach (self::$configFileNames as $file) {
            $file = $this->cwd . DIRECTORY_SEPARATOR . $file;

            if (file_exists($file)) {
                $config = json_decode(file_get_contents($file), true);
                if ($config !== null) {
                    return $config;
                }
                throw new \Exception("[$file] contains invalid JSON");
            }
        }

        return [];
    }

    /**
     * @param $config
     * @return ConfiguredTestRunner
     */
    protected function createTestRunner($config) {
        return new ConfiguredTestRunner($this->cwd, $config);
    }
}
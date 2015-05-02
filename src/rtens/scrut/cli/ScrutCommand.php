<?php
namespace rtens\scrut\cli;

class ScrutCommand {

    private static $configFileNames = [
        'scrut.json',
        'scrut.json.dist'
    ];

    /** @var string */
    private $cwd;

    /** @var array|string[] */
    private $argv;

    /**
     * @param string $cwd
     * @param array|string[] $argv
     */
    public function __construct($cwd, array $argv) {
        $this->cwd = $cwd;
        $this->argv = $argv;
    }

    public function execute() {
        $config = [];

        foreach (self::$configFileNames as $file) {
            $file = $this->cwd . DIRECTORY_SEPARATOR . $file;

            if (file_exists($file)) {
                $config = json_decode(file_get_contents($file), true);
                if ($config === null) {
                    die("[$file] contains invalid JSON");
                }
                break;
            }
        }

        if (count($this->argv) > 1) {
            $config['suite']['file'] = $this->argv[1];
        }

        $runner = new ConfiguredTestRunner($this->cwd, $config);
        return $runner->run() ? 0 : 1;
    }
}
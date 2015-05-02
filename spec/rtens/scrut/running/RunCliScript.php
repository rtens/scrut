<?php
namespace rtens\scrut\running;

use rtens\scrut\Asserter;

/**
 * @property \rtens\scrut\fixtures\FilesFixture files <-
 */
class RunCliScript {

    private $output = null;
    private $return = null;
    private $cwd;

    function before() {
        $this->cwd = $this->files->givenTheFolder('foo');
    }

    function noAutoloadFound(Asserter $assert) {
        $this->runTheScript();

        $assert($this->return, 2);
        $assert($this->output, [
            'Error: None of these auto-loaders found: [bootstrap.php, autoload.php, vendor/autoload.php]'
        ]);
    }

    function findBootstrap(Asserter $assert) {
        $this->files->givenTheFile_Containing('foo/bootstrap.php', $this->autoloadCode('echo "found it";'));

        $this->runTheScript();
        $assert($this->output, ['found it']);
    }

    function findLocalAutoload(Asserter $assert) {
        $this->files->givenTheFile_Containing('foo/autoload.php', $this->autoloadCode('echo "auto-loaded";'));

        $this->runTheScript();
        $assert($this->output, ['auto-loaded']);
    }

    function findVendorAutoload(Asserter $assert) {
        $this->files->givenTheFile_Containing('foo/autoload.php', $this->autoloadCode('echo "vendor";'));

        $this->runTheScript();
        $assert($this->output, ['vendor']);
    }

    function exitWithReturnValue(Asserter $assert) {
        $this->files->givenTheFile_Containing('foo/autoload.php', $this->autoloadCode('return 42;'));

        $this->runTheScript();
        $assert($this->return, 42);
    }

    private function runTheScript() {
        $script = realpath(__DIR__ . '/../../../../bin/scrut');
        exec('cd ' . $this->cwd . ' && ' . PHP_BINARY . ' ' . $script, $this->output, $this->return);
    }

    private function autoloadCode($code) {
        return '<?php namespace rtens\scrut\cli;
            class ScrutCommand {
                function execute() {
                    ' . $code . '
                }
            }
        ';
    }
}
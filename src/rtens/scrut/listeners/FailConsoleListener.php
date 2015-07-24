<?php
namespace rtens\scrut\listeners;

use rtens\scrut\results\FailedTestResult;
use rtens\scrut\TestName;
use rtens\scrut\TestResult;

class FailConsoleListener extends ConsoleListener {

    private $failed = 0;

    private $ran = 0;

    public function onResult(TestName $test, TestResult $result) {
        parent::onResult($test, $result);
        $this->ran++;

        if ($result instanceof FailedTestResult) {
            $this->failed++;
            $failureSource = $result->getFailure()->getFailureSource();

            $this->printLine();
            $this->printLine("FAILED: " . $test);
            $this->printLine('   Source:');
            $this->printLine('      ' . $failureSource);

            if ($pos = strrpos($failureSource, ':')) {
                $file = substr($failureSource, 0, $pos);
                $line = substr($failureSource, $pos + 1);

                if (file_exists($file)) {
                    $content = explode("\n", file_get_contents($file));

                    if (array_key_exists($line - 1, $content)) {
                        $this->printLine('   Code:');
                        $this->printLine('      ' . trim($content[$line - 1]));
                    }
                }
            }

            $this->printLine('   Message:');
            $this->printNotEmptyLine('      ' . $result->getFailure()->getFailureMessage());
            $this->printNotEmptyLine('      ' . $result->getFailure()->getMessage());
        }
    }

    protected function onEnd() {
        $this->printLine();
        $this->printLine($this->ran . ' ran, '
            . ($this->failed ? $this->failed . ' FAILED =(' : 'none failed =D'));
    }
}
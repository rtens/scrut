<?php
namespace rtens\scrut\fixtures;

use rtens\scrut\Assert;
use rtens\scrut\Fixture;
use watoki\factory\Factory;

class FilesFixture extends Fixture {

    private $tmp;

    /**
     * @param Assert $assert <-
     * @param Factory $factory <-
     */
    function __construct(Assert $assert, Factory $factory) {
        parent::__construct($assert);
        $factory->setSingleton($this);
    }

    public function fullPath($path = '') {
        if (!$this->tmp) {
            $this->tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('scrut_tmp_') . DIRECTORY_SEPARATOR;
            @mkdir($this->tmp);
        }
        return rtrim($this->tmp . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path), '\\/');
    }

    public function after() {
        $this->clear();
    }

    public function givenTheFolder($path) {
        $fullPath = $this->fullPath($path);
        @mkdir($fullPath, 0777, true);
        return $fullPath;
    }

    public function givenTheFile_Containing($fileName, $content) {
        $this->givenTheFolder(dirname($fileName));
        $fullPath = $this->fullPath($fileName);
        file_put_contents($fullPath, $content);
        return $fullPath;
    }

    public function thenThereShouldBeAFile_Containing($fileName, $content) {
        $this->thenThereShouldBeAFile($fileName);
        $this->assert->equals(file_get_contents($this->fullPath($fileName)), $content);
    }

    public function thenThereShouldBeNoFile($fileName) {
        $this->assert->not()->isTrue(file_exists($this->fullPath($fileName)));
    }

    public function thenThereShouldBeAFile($fileName) {
        $this->assert->isTrue(file_exists($this->fullPath($fileName)) && is_file($this->fullPath($fileName)));
    }

    public function thenThereShouldBeAFolder($folderName) {
        $this->assert->isTrue(file_exists($this->fullPath($folderName)) && is_dir($this->fullPath($folderName)));
    }

    public function clear($path = "") {
        $this->_clear($this->fullPath($path));
    }

    private function _clear($dir) {
        if (!file_exists($dir)) {
            return;
        }

        /** @var \DirectoryIterator $file */
        foreach (new \DirectoryIterator($dir) as $file) {
            if ($file->isFile()) {
                unlink($file->getRealPath());
            } else if (!$file->isDot()) {
                $this->_clear($file->getRealPath());
            }
        }
        rmDir($dir);
    }
}
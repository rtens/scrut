<?php
namespace rtens\scrut\fixtures;

use rtens\scrut\Fixture;

class FilesFixture extends Fixture {

    private $tmp;

    protected function init() {
        $this->tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('scrut_tmp_') . DIRECTORY_SEPARATOR;
    }

    public function givenTheFolder($path) {
        @mkdir($this->fullPath($path), 0777, true);
    }

    public function givenTheFile_Containing($fileName, $content) {
        $this->givenTheFolder(dirname($fileName));
        file_put_contents($this->fullPath($fileName), $content);
    }

    public function fullPath($path) {
        return $this->tmp . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }

    public function clear($path = "") {
        $this->_clear($this->fullPath($path));
    }

    private function _clear($dir) {
        if (!file_exists($dir)) {
            return;
        }

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
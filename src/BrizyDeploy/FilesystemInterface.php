<?php

namespace BrizyDeploy;

interface FilesystemInterface
{
    /**
     * @param array $patterns
     */
    static public function deleteFilesByPattern(array $patterns);

    /**
     * @param $src
     * @param $dst
     * @return mixed
     */
    static public function copyDirectory($src, $dst);

    /**
     * @param $file
     * @return mixed
     */
    static function fileExists($file);

    /**
     * @param $source
     * @param $dest
     * @return mixed
     */
    static function copyFile($source, $dest);
}
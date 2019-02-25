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
}
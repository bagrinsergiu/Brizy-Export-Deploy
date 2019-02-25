<?php

namespace BrizyDeploy;

class Filesystem implements FilesystemInterface
{
    /**
     * @param array $patterns
     */
    static public function deleteFilesByPattern(array $patterns)
    {
        foreach ($patterns as $pattern) {
            $files = glob($pattern);
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    static public function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
<?php

function deleteFilesByPattern(array $patterns)
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

function copyDirectory($src, $dst)
{
    $dir = opendir($src);
    if ($dir === false) {
        return false;
    }

    @mkdir($dst, 0755);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDirectory($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);

    return true;
}

function recursiveRemoveDir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object))
                    recursiveRemoveDir($dir . "/" . $object);
                else
                    unlink($dir . "/" . $object);
            }
        }
        rmdir($dir);
    }
}

function removeDirectory($path) {
    $files = glob($path . '/*');
    foreach ($files as $file) {
        is_dir($file) ? removeDirectory($file) : unlink($file);
    }
    rmdir($path);
    return;
}

function post_deploy_action($params)
{
    removeDirectory($params['source_current']);
    if ($params['success']) {
        copyDirectory($params['source_latest'], $params['dist']);
    } else {
        copyDirectory($params['source_backup'], $params['dist']);
    }

    recursiveRemoveDir($params['source_latest']);
    recursiveRemoveDir($params['source_backup']);
}
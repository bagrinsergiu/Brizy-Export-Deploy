<?php

require_once 'src/Config.php';

$config = new Config();
if ($config->isInstalled() === true) {
    echo file_get_contents('cache/page.html');
} else {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Location: http://{$_SERVER['HTTP_HOST']}/install.php");
}

exit;
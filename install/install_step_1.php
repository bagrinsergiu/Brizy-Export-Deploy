<?php

use Symfony\Component\HttpFoundation\RedirectResponse;
use BrizyDeploy\Filesystem;
use Symfony\Component\HttpFoundation\Request;

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

$request = Request::createFromGlobals();

if (!Filesystem::fileExists(__DIR__ . '/../var/config.json')) {
    Filesystem::copyFile(__DIR__ . '/../app/config/config.json.dist', __DIR__ . '/../var/config.json');
}

$response = new RedirectResponse("{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/install/install_step_2.php");
$response->send();

exit;
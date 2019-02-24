<?php

use BrizyDeploy\App;
use BrizyDeploy\Exception\AppException;
use BrizyDeploy\Http\Response;
use BrizyDeploy\Http\RedirectResponse;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

try {
    $app = new App();
} catch (AppException $e) {
    $response = new Response($e->getMessage(), 500);
    $response->send();
    exit;
}

if ($app->isInstalled() === true) {
    $html = file_get_contents(__DIR__ . '/cache/page.html');
    $response = new Response($html, 200);
    $response->send();
} else {
    $targetUrl = "http://{$_SERVER['HTTP_HOST']}/install/install_step_1.php";
    $response = new RedirectResponse($targetUrl);
    $response
        ->addHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->addHeader('Cache-Control', 'post-check=0, pre-check=0')
        ->addHeader('Pragma', 'no-cache');

    $response->send();
}

exit;
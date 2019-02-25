<?php

use BrizyDeploy\App;
use BrizyDeploy\Deploy;
use BrizyDeploy\Exception\AppException;
use BrizyDeploy\Http\Response;
use BrizyDeploy\Http\RedirectResponse;

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
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
    $response = new RedirectResponse("{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}");
    $response->send();
    exit;
}

$deploy = new Deploy();
$deploy->execute();
if (!$deploy->isSucceeded()) {
    $errors = $deploy->getErrors();
    $response = new Response(json_encode($errors), 400);
    $response->setHeaders([[
        'Content-Type' => 'application/json'
    ]]);
    $response->send();
    exit;
}

$app->setIsInstalled(true);
$app->saveConfig();

$response = new RedirectResponse("{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}");
$response->send();
exit;

<?php

use BrizyDeploy\Http\Response;
use BrizyDeploy\Deploy;
use BrizyDeploy\App;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

$app = new App();
if ($app->isInstalled() === false) {
    $response = new Response('App was not installed.', 500);
    $response->send();
    exit;
}

$deploy = new Deploy($app->getBrizyCloudUrl(), $app->getProjectHashId());

try {
    $deploy->execute();
} catch (\Exception $e) {
    $response = new Response($e->getMessage(), 500);
    $response->send();
    exit;
}

if (!$deploy->isSucceeded()) {
    $errors = $deploy->getErrors();
    $response = new Response(json_encode($errors), 400);
    $response->setHeaders([[
        'Content-Type' => 'application/json'
    ]]);
    $response->send();
    exit;
}

$response = new Response('Successfully deployed.', 200);
exit;

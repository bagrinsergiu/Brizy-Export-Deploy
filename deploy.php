<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

use BrizyDeploy\Http\Response;
use BrizyDeploy\Deploy;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

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

$response = new Response('Successfully deployed.', 200);
exit;

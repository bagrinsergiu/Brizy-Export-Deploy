<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use BrizyDeploy\Deploy;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

require_once __DIR__ . '/app/AppKernel.php';

$appKernel = new AppKernel();
if ($appKernel->isInstalled() === false) {
    $response = new Response('App was not installed.', 500);
    $response->send();
    exit;
}

$deploy = new Deploy($appKernel->getDeployUrl(), $appKernel->getAppId());

try {
    $deploy->execute();
} catch (\Exception $e) {
    $response = new Response($e->getMessage(), 500);
    $response->send();
    exit;
}

if (!$deploy->isSucceeded()) {
    $errors = $deploy->getErrors();
    $response = new JsonResponse(json_encode($errors), 400);
    $response->send();
    exit;
}

$response = new Response('Successfully deployed.', 200);
exit;

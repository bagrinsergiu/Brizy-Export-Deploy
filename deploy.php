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
require_once 'utils.php';

$appKernel = new AppKernel();
if ($appKernel->isInstalled() === false) {
    $response = new Response('App was not installed.', 500);
    $response->send();
    exit;
}

$deploy = new Deploy($appKernel->getDeployUrl(), $appKernel->getAppId());

try {
    $deployed = $deploy->execute();
} catch (\Exception $e) {
    $response = new Response($e->getMessage() . " in " . $e->getFile(), 500);
    $response->send();
    exit;
}

if (!$deployed) {
    $errors = $deploy->getErrors();
    $response = new JsonResponse($errors, 400);
    $response->send();
    exit;
}

$response = new Response('Successfully deployed.', 200);
$response->send();
exit;

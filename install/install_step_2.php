<?php

use BrizyDeploy\Deploy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use BrizyDeploy\Utils\HttpUtils;

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

require_once __DIR__ . '/../app/AppKernel.php';

$request = Request::createFromGlobals();

$app = new AppKernel();
if ($app->isInstalled() === true) {
    $response = new RedirectResponse(HttpUtils::getBaseUrl($request, '/install/install_step_2.php', ''));
    $response->send();
    exit;
}

$deploy = new Deploy($app->getBrizyCloudUrl(), $app->getProjectHashId());

try {
    $deploy->execute();
} catch (\Exception $e) {
    $response = new Response($e->getMessage()." in ".$e->getFile(), 500);
    $response->send();
    exit;
}

if (!$deploy->isSucceeded()) {
    $errors = $deploy->getErrors();
    $response = new JsonResponse(json_encode($errors), 400);
    $response->send();
    exit;
}

$app->setIsInstalled(true);
$app->saveConfig();

$response = new RedirectResponse(HttpUtils::getBaseUrl($request, '/install/install_step_2.php', ''));
$response->send();
exit;

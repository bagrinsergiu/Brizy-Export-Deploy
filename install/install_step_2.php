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
require_once __DIR__ . '/../utils.php';

$request = Request::createFromGlobals();

$appKernel = new AppKernel();
if ($appKernel->isInstalled() === true) {
    $response = new RedirectResponse(HttpUtils::getBaseUrl(
        $request,
        '/install/install_step_2.php',
        ''
    ));
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

$appKernel->setIsInstalled(true);
$appKernel->saveConfig();

$response = new RedirectResponse(HttpUtils::getBaseUrl(
    $request,
    '/install/install_step_2.php',
    ''
));
$response->send();
exit;

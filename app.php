<?php

use BrizyDeploy\Modal\DeployRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use BrizyDeploy\Utils\HttpUtils;
use BrizyDeploy\Deploy;
use BrizyDeploy\Modal\AppRepository;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

require_once __DIR__ . '/app/Kernel.php';

$request = Request::createFromGlobals();

// @todo is installed?

// @todo is updated?

// @todo is deployed?

$appRepository = new AppRepository();
$app = $appRepository->get();

if (!$app || !$app->getInstalled() || !Kernel::isInstalled()) {
    $response = new RedirectResponse(HttpUtils::getBaseUrl(
        $request,
        '',
        '/install/install_step_1.php'
    ));

    $response->setPrivate();
    $response->setMaxAge(0);
    $response->headers->addCacheControlDirective('must-revalidate', true);
    $response->headers->addCacheControlDirective('no-store', true);
    $response->send();
}

$deployRepository = new DeployRepository();
$deploy = $deployRepository->get();
if ($deploy->getTimestamp() === null || $deploy->getExecute()) {
    $deployService = new Deploy($app->getDeployUrl(), $app->getAppId());

    try {
        $deployed = $deployService->execute();
    } catch (\Exception $e) {
        $response = new Response($e->getMessage() . " in " . $e->getFile(), 500);
        $response->send();
        exit;
    }

    if (!$deployed) {
        $errors = $deployService->getErrors();
        $response = new JsonResponse($errors, 400);
        $response->send();
        exit;
    }

    $deploy->setExecute(false);
    $deploy->setTimestamp(time());
    $deployRepository->update($deploy);
}

$html = file_get_contents(__DIR__ . '/cache/index.html');
$response = new Response($html, 200);
$response->send();

exit;

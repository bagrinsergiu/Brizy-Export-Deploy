<?php

use BrizyDeploy\Deploy;
use BrizyDeploy\Modal\DeployRepository;
use BrizyDeploy\Modal\AppRepository;
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

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../app/Kernel.php';

$request = Request::createFromGlobals();

$appRepository = new AppRepository();
$app = $appRepository->get();
if (!Kernel::isInstalled()) {
    $response = new RedirectResponse(HttpUtils::getBaseUrl(
        $request,
        '/install/install_step_2.php',
        '/install/install_step_1.php'
    ));
    $response->send();
    exit;
}

$deployRepository = new DeployRepository();
$deploy = $deployRepository->get();
if ($deploy->getTimestamp() !== null) {
    $response = new RedirectResponse(HttpUtils::getBaseUrl(
        $request,
        '/install/install_step_2.php',
        ''
    ));
    $response->send();
    exit;
}

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

$app->setInstalled(true);
$appRepository->update($app);

$response = new RedirectResponse(HttpUtils::getBaseUrl(
    $request,
    '/install/install_step_2.php',
    ''
));
$response->send();
exit;

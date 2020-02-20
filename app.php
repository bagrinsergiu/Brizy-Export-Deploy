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
require_once 'app/utils.php';

$request = Request::createFromGlobals();

$appRepository = new AppRepository();
$app = $appRepository->get();
if (!$app || !$app->getInstalled() || !Kernel::isInstalled()) {
    $response = new RedirectResponse(HttpUtils::getBaseUrl(
        $request,
        '',
        '/install_step_1.php'
    ));

    $response->setPrivate();
    $response->setMaxAge(0);
    $response->headers->addCacheControlDirective('must-revalidate', true);
    $response->headers->addCacheControlDirective('no-store', true);
    $response->send();
    exit;
}

$baseUrl = HttpUtils::getBaseUrl(
    $request,
    '',
    ''
);

if ($app->getBaseUrl() != $baseUrl) {
    $response = new JsonResponse([
        'message' => 'Invalid base url'
    ], 400);
    $response->send();
    exit;
}

$deployRepository = new DeployRepository();
$deploy = $deployRepository->get();
if ($deploy && $deploy->getExecute()) {
    $deployService = new Deploy($app->getDeployUrl(), $app->getAppId());
    $zipInfo = $deployService->getZipInfo();
    $deploy->setZipInfoTimestamp(time());
    $deployRepository->update($deploy);
    if (!$zipInfo) {
        $response = new Response('Sync in processing. It may take a some time.', 400);
        $response->send();
        exit;
    }

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

if ($deploy && $deploy->getUpdate()) {
    $deployService = new Deploy($app->getDeployUrl(), $app->getAppId());
    if ((time() - Deploy::ZIP_INFO_INTERVAL) > $deploy->getZipInfoTimestamp()) {
        $zipInfo = $deployService->getZipInfo();
        $deploy->setZipInfoTimestamp(time());
        $deployRepository->update($deploy);
        if ($zipInfo) {
            $deploy->setExecute(true);
            $deploy->setUpdate(false);
            $deploy->setTimestamp(time());
            $deployRepository->update($deploy);
            $response = new RedirectResponse(HttpUtils::getBaseUrl(
                $request,
                '',
                ''
            ));

            $response->setPrivate();
            $response->setMaxAge(0);
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->headers->addCacheControlDirective('no-store', true);
            $response->send();
            exit;
        }
    }
}

if (!$page = $request->query->get('page')) {
    $page = 'index';
}

$html = file_get_contents(__DIR__ . '/cache/' . $page . '.html');
if (!$html) {
    $response = new Response("Page was not found", 404);
    $response->send();
    exit;
}

$url = $request->getUri();
$html = str_replace(
    [
        '{{ brizy_dc_page_language }}',
        '{{ brizy_dc_current_page_unique_url }}',
        '{{ brizy_dc_group_language }}',
        '{{ site_url }}'
    ],
    [
        'en',
        $url,
        'en',
        $url
    ],
    $html
);

$response = new Response($html, 200);
$response->send();

exit;

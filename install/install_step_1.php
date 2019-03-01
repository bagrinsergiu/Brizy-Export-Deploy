<?php

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use BrizyDeploy\Utils\HttpUtils;
use Symfony\Component\HttpFoundation\Response;

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

$request = Request::createFromGlobals();

require_once __DIR__ . '/../app/AppKernel.php';

$appKernel = new AppKernel();
$appKernel->init();

#test two-sided connection with remote server
$client = HttpUtils::getHttpClient();
$appUrl = HttpUtils::getBaseUrl($request, '/install/install_step_1.php', '/connect.php');
$url = $appKernel->getDeployUrl() . '/projects/' . $appKernel->getAppId() . '/export/check-connection';
$response = $client->post($url, [
    'body' => [
        'url' => $appUrl
    ]
]);

if ($response->getStatusCode() != 200) {
    $response = new Response($response->getBody()->getContents());
    $response->send();
    exit;
}

$response = new RedirectResponse(HttpUtils::getBaseUrl(
    $request,
    '/install/install_step_1.php',
    '/install/install_step_2.php'
));
$response->send();

exit;
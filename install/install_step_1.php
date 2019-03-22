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

HttpUtils::getClientIP($request) == '127.0.0.1' ? $is_localhost = 1 : $is_localhost = 0;

#test two-sided connection with remote server
$client = HttpUtils::getHttpClient();
$connectUrl = HttpUtils::getBaseUrl($request, '/install/install_step_1.php', '/connect.php');
$baseUrl = HttpUtils::getBaseUrl($request, '/install/install_step_1.php', '');
$url = $appKernel->getDeployUrl() . '/export/check-connection';
$response = $client->post($url, [
    'body' => [
        'base_url' => $baseUrl,
        'connect_url' => $connectUrl,
        'project_uid' => $appKernel->getAppId(),
        'is_localhost' => 0 //$is_localhost
    ]
]);

if ($response->getStatusCode() != 200) {
    $response = new Response('Connection error: ' . $response->getBody()->getContents());
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
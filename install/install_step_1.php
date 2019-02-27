<?php

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use BrizyDeploy\Utils\HttpUtils;

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

$request = Request::createFromGlobals();

require_once __DIR__ . '/../app/AppKernel.php';

$app = new AppKernel();
$app->init();

$response = new RedirectResponse(HttpUtils::getBaseUrl($request, '/install/install_step_1.php', '/install/install_step_2.php'));
$response->send();

exit;
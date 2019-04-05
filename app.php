<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use BrizyDeploy\Utils\HttpUtils;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

require_once __DIR__ . '/app/AppKernel.php';

$request = Request::createFromGlobals();

$appKernel = new AppKernel();
if ($appKernel->isInstalled() === true) {
    $html = file_get_contents(__DIR__ . '/cache/index.html');
    $response = new Response($html, 200);
    $response->send();
} else {
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

exit;

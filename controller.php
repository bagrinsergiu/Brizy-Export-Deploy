<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/app/BrizyDeployRequirements.php';
require_once 'utils.php';

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

//require_once __DIR__ . '/app/AppKernel.php';
//
//$appKernel = new AppKernel();
//if ($appKernel->isInstalled() === false) {
//    $response = new Response('App was not installed.', 500);
//    $response->send();
//    exit;
//}

$request = Request::createFromGlobals();

$action = $request->get('action');
switch ($action) {
    case 'update':

        break;
    case 'deploy':

        break;
    default:
        $response = new Response('Undefined action', 404);
        $response->send();
        exit;
}

$response = new Response('controller.php. Action: '.$action);
$response->send();
exit;
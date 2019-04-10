<?php

use BrizyDeploy\Modal\DeployRepository;
use Symfony\Component\HttpFoundation\Response;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

$deployRepository = new DeployRepository();
$deploy = $deployRepository->get();
if (!$deploy) {
    $response = new Response('Error.', 400);
    $response->send();
    exit;
}

$deploy->setExecute(true);
$deployRepository->update($deploy);

$response = new Response('Successfully deployed.', 200);
$response->send();
exit;

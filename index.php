<?php

use BrizyDeploy\App;
use BrizyDeploy\Exception\AppException;
use BrizyDeploy\Http\Response;

require __DIR__.'/autoload.php';

try {
    $app = new App();
} catch (AppException $e) {
    $response = new Response($e->getMessage(), 500);
    $response->send();
    exit;
}

if ($app->isInstalled() === true) {
    $html = file_get_contents('cache/page.html');
    $response = new Response($html, 200);
    $response->send();
} else {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Location: http://{$_SERVER['HTTP_HOST']}/install/install_step_1.php");
}

exit;
<?php

use BrizyDeploy\App;
use BrizyDeploy\Filesystem;
use BrizyDeploy\Deploy;
use BrizyDeploy\Utils\HttpUtils;
use BrizyDeploy\Exception\AppException;
use BrizyDeploy\Http\Response;

require __DIR__ . '/../autoload.php';

try {
    $app = new App();
} catch (AppException $e) {
    $response = new Response($e->getMessage(), 500);
    $response->send();
    exit;
}

if ($app->isInstalled() === true) {
    header("Location: http://{$_SERVER['HTTP_HOST']}");
    exit;
}

//@todo create reserve copy, etc.
$filesystem = new Filesystem();
$filesystem->deleteFilesByPattern([
    __DIR__.'/../cache/*',
    __DIR__.'/../cache/img/*'
]);

$deploy = new Deploy();
$deploy->execute();
if (!$deploy->isSucceeded()) {
    $errors = $deploy->getErrors();
    header(HttpUtils::getHttpStatus(400));
    var_dump($errors);
    exit;
}

$app->setIsInstalled(true);
$app->saveConfig();

header("Location: http://{$_SERVER['HTTP_HOST']}");
exit;

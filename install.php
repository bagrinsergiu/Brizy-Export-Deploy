<?php

require_once 'app/BrizyDeployRequirements.php';
require_once 'src/Config.php';
require_once 'src/HttpUtils.php';
require_once 'src/Deploy.php';
require_once 'src/Filesystem.php';

$config = new Config();
if ($config->isInstalled() === true) {
//    header("Location: http://{$_SERVER['HTTP_HOST']}");
//    exit;
}

$brizyDeployRequirements = new BrizyDeployRequirements();

$majorProblems = $brizyDeployRequirements->getFailedRequirements();
$minorProblems = $brizyDeployRequirements->getFailedRecommendations();
$hasMajorProblems = (bool) count($majorProblems);
$hasMinorProblems = (bool) count($minorProblems);

if ($hasMajorProblems || $hasMinorProblems) {
    header(HttpUtils::getHttpStatus(400));
    var_dump($majorProblems);
    var_dump($minorProblems);
    exit;
}

//@todo create reserve copy, etc.
$filesystem = new Filesystem();
$filesystem->deleteFilesByPattern([
    __DIR__.'/cache/*',
    __DIR__.'/cache/img/*'
]);

//@todo handle deploy errors
$deploy = new Deploy();
$deploy->execute();
if (!$deploy->isSucceeded()) {
    $errors = $deploy->getErrors();
}

$config->setIsInstalled(true);
$config->save();

//header("Location: http://{$_SERVER['HTTP_HOST']}");
exit;

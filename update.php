<?php

use Symfony\Component\HttpFoundation\Request;
use BrizyDeploy\Update\Update;
use Symfony\Component\HttpFoundation\JsonResponse;

require_once __DIR__ . '/app/BrizyDeployRequirements.php';

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit;
}

require $composerAutoload;

$brizyDeployRequirements = new BrizyDeployRequirements();

$majorProblems = $brizyDeployRequirements->getFailedRequirements();
$minorProblems = $brizyDeployRequirements->getFailedRecommendations();
$hasMajorProblems = (bool) count($majorProblems);
$hasMinorProblems = (bool) count($minorProblems);

if ($hasMajorProblems || $hasMinorProblems) {
    $response = new JsonResponse($majorProblems, 400);
    $response->send();
    exit;
}

$request = Request::createFromGlobals();

$update = new Update();
$result = $update->execute();
if ($result) {
    $response = new JsonResponse([
        'success' => true
    ], 200);
} else {
    $response = new JsonResponse($update->getErrors(), 400);
}

$response->send();

exit;

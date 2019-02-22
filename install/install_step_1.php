<?php

require_once __DIR__ . '/../app/BrizyDeployRequirements.php';

$brizyDeployRequirements = new BrizyDeployRequirements();

$majorProblems = $brizyDeployRequirements->getFailedRequirements();
$minorProblems = $brizyDeployRequirements->getFailedRecommendations();
$hasMajorProblems = (bool) count($majorProblems);
$hasMinorProblems = (bool) count($minorProblems);

if ($hasMajorProblems || $hasMinorProblems) {
    var_dump($majorProblems);
    var_dump($minorProblems);
    exit;
} else {
    if (!is_file(__DIR__ . '/../var/config.json')) {
        copy(__DIR__ . '/../config.json.dist', __DIR__ . '/../var/config.json');
    }
    header("Location: http://{$_SERVER['HTTP_HOST']}/install/install_step_2.php");
    exit;
}
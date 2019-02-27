<?php

require_once __DIR__ . '/app/BrizyDeployRequirements.php';
require_once __DIR__ . '/app/AppKernel.php';

$appKernel = new AppKernel();
if ($appKernel->isInit()) {
    require_once 'app.php';
    exit;
}

$brizyDeployRequirements = new BrizyDeployRequirements();

$majorProblems = $brizyDeployRequirements->getFailedRequirements();
$minorProblems = $brizyDeployRequirements->getFailedRecommendations();
$hasMajorProblems = (bool) count($majorProblems);
$hasMinorProblems = (bool) count($minorProblems);

if ($hasMajorProblems || $hasMinorProblems) {
    var_dump($majorProblems);
    var_dump($minorProblems);
} else {
    require_once 'app.php';
}

exit;
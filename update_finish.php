<?php

require_once 'utils.php';

$params = [
    'delete_files' => [
        __DIR__ . '/../../*'
    ],
    'source_latest' => __DIR__ . '/../../var/script_latest',
    'source_backup' => __DIR__ . '/../../var/script_backup',
    'dist' => realpath(__DIR__ . '/../../'),
    'success' => true
];

post_deploy_action($params);
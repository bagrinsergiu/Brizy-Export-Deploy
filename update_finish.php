<?php

require_once 'utils.php';

$params = [
    'source_current' => __DIR__,
    'source_latest' => sys_get_temp_dir() . '/script_latest',
    'source_backup' => sys_get_temp_dir() . '/script_backup',
    'dist' => realpath(__DIR__),
    'success' => true
];

post_deploy_action($params);
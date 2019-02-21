<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

function getResponse($http_code, $http_text, $error_message)
{
    header("HTTP/1.0 {$http_code} {$http_text}");
    echo $error_message;
    exit;
}

function deleteFiles(array $patterns)
{
    foreach ($patterns as $pattern) {
        $files = glob($pattern);
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }
        }
    }
}

if (!extension_loaded('zip')) {
    getResponse(400, 'Bad Request', "PHP Module 'zip' was not installed.");
}

if (
    $_SERVER['REQUEST_METHOD'] != 'POST' ||
    !isset($_SERVER['HTTP_X_SIGNATURE']) ||
    $_SERVER['HTTP_X_SIGNATURE'] != 'xxx'
) {
    getResponse(401, 'Unauthorized', 'Unauthorized.');
}

if (!isset($_FILES['site'])) {
    getResponse(404, 'Not Found', "File 'site' was not found.");
}

if ($_FILES['site']['error'] != 0) {
    switch ($_FILES['site']['error']) {
        case UPLOAD_ERR_INI_SIZE:
            $message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
            break;
        case UPLOAD_ERR_PARTIAL:
            $message = 'The uploaded file was only partially uploaded.';
            break;
        case UPLOAD_ERR_NO_FILE:
            $message = 'No file was uploaded.';
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $message = 'Missing a temporary folder.';
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $message = 'Failed to write file to disk.';
            break;
        case UPLOAD_ERR_EXTENSION:
            $message = 'A PHP extension stopped the file upload. 
            PHP does not provide a way to ascertain which extension caused the file upload to stop; 
            examining the list of loaded extensions with phpinfo() may help.';
            break;

        default:
            $message = 'Undefined file error';
    }

    getResponse(400, 'Bad Request', $message);
}

//@todo create reserve copy
deleteFiles([
    __DIR__.'/cache/*',
    __DIR__.'/cache/img/*'
]);

$errors = array();
$zip = zip_open($_FILES['site']['tmp_name']);
if (!is_resource($zip)) {
    getResponse(400, 'Bad Request', 'Invalid zip.');
}

while ($zip_entry = zip_read($zip)) {
    $name = zip_entry_name($zip_entry);
    if (!preg_match("/\/$/", $name)) {
        $asset_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        $bytes = file_put_contents(__DIR__ . '/' . $name, $asset_content);
        if ($bytes === false) {
            $errors[] = $name;
        }
    }
}

zip_close($zip);

if (count($errors) > 0) {
    getResponse(
        400,
        'Bad Request',
        "Some files was not updated: " . implode(',', $errors) . '. Check if a directory exists and file is writable.'
    );
}

getResponse(200, 'Ok', 'Successfully deployed.');
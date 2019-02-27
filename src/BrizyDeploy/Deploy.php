<?php

namespace BrizyDeploy;

use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;

class Deploy implements DeployInterface
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var bool
     */
    protected $is_succeeded = true;

    /**
     * @var string
     */
    protected $brizy_cloud_url;

    /**
     * @var string
     */
    protected $project_hash_id;

    public function __construct($brizy_cloud_url, $project_hash_id)
    {
        $this->brizy_cloud_url = $brizy_cloud_url;
        $this->project_hash_id = $project_hash_id;
    }

    public function execute()
    {
        $zip_path = $this->getZipPath();
        $zip = zip_open($zip_path);
        if (!is_resource($zip)) {
            $this->errors['general'][] = 'Invalid zip resource';
            $this->is_succeeded = false;
        }

        while ($zip_entry = zip_read($zip)) {
            $name = zip_entry_name($zip_entry);
            if (!preg_match("/\/$/", $name)) {
                $asset_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                $bytes = file_put_contents(__DIR__ . '/../../var/' . $name, $asset_content);
                if ($bytes === false) {
                    $this->errors['files'][] = $name;
                }
            }
        }

        zip_close($zip);

        if (count($this->errors) > 0) {
            //restore cache from revision
            Filesystem::copyDirectory(__DIR__ . '/../../var/cache_revision',__DIR__ . '/../../var/cache');
            $this->is_succeeded = false;
        }

        Filesystem::recursiveRemoveDir(__DIR__ . '/../../var/cache_revision');
        Filesystem::removeFile($zip_path);
    }

    /**
     * @return string
     */
    protected function getZipPath()
    {
        $zip_name = __DIR__ . '/../../var/brizy-' . time() . '.zip';
        $resource = fopen($zip_name, 'w');
        $stream = Stream::factory($resource);
        $client = new Client();

        $response = $client->get(
            $this->brizy_cloud_url . '/projects/' . $this->project_hash_id . '/export',
            ['save_to' => $stream]
        );
        if ($response->getStatusCode() != 200) {
            $this->errors['general'][] = 'Zip was not downloaded';
            return null;
        }

        //create revision
        Filesystem::copyDirectory(__DIR__ . '/../../var/cache', __DIR__ . '/../../var/cache_revision');
        //clear cache
        Filesystem::deleteFilesByPattern([
            __DIR__ . '/../../var/cache/*',
            __DIR__ . '/../../var/cache/img/*'
        ]);

        return $zip_name;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isSucceeded()
    {
        return $this->is_succeeded;
    }
}
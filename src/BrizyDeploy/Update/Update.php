<?php

namespace BrizyDeploy\Update;

use BrizyDeploy\Utils\HttpUtils;
use GuzzleHttp\Stream\Stream;
use BrizyDeploy\Filesystem;

class Update implements UpdateInterface
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var string
     */
    private $backup_path;

    public function __construct()
    {
        $this->backup_path = __DIR__ . '/../../../var/backup.zip';

        $this->postExecuteRemove($this->backup_path);

        ini_set('max_execution_time', 120);
        ini_set('memory_limit','256M');
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function execute()
    {
        #backup
        $backup = $this->backup(__DIR__ . '/../../../', $this->backup_path);
        if (!$backup) {
            $this->errors['backup'] = 'backup was not created';
            return false;
        }

        #download
        $zip_path = $this->getZipPath('https://s3.amazonaws.com/bitblox-develop/brizy.zip');

        try {
            #execute
            $result = $this->install($zip_path);
            if (!$result) {
                #restore from backup
                $this->install($this->backup_path);
                return false;
            }
        } catch (\Exception $e) {
            #restore from backup
            $this->install($this->backup_path);
            return false;
        }

        return true;
    }

    protected function install($zip_path)
    {
        $zip = zip_open($zip_path);
        if (!is_resource($zip)) {
            $this->errors['error']['zip'][] = 'Invalid zip';
            return false;
        }

        $result = true;
        while ($zip_entry = zip_read($zip)) {
            $name = zip_entry_name($zip_entry);
            if (!preg_match("/\/$/", $name)) {
                $asset_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                $name = __DIR__ . '/../../../' . str_replace('brizy/', '', $name);

                $dirname = dirname($name);

                if (!is_dir($dirname)) {
                    Filesystem::recursiveCreateDir($dirname);
                }

                if (file_exists($name) && !is_writable($name)) {
                    $this->errors['error']['permissions'][] = $name;
                    $result = false;
                }

                $bytes = file_put_contents($name, $asset_content);
                if ($bytes === false) {
                    $this->errors['error']['files'][] = $name;
                    $result = false;
                }
            }
        }

        zip_close($zip);

        return $result;
    }

    protected function backup($source, $destination)
    {
        if (extension_loaded('zip') === true) {
            if (file_exists($source) === true) {
                $zip = new \ZipArchive();
                if ($zip->open($destination, \ZIPARCHIVE::CREATE) === true) {
                    $source = realpath($source);
                    if (is_dir($source) === true) {
                        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($files as $file) {
                            $file = realpath($file);
                            if (is_dir($file) === true) {
                                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                            } else if (is_file($file) === true) {
                                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                            }
                        }
                    } else if (is_file($source) === true) {
                        $zip->addFromString(basename($source), file_get_contents($source));
                    }
                }
                return $zip->close();
            }
        }

        return false;
    }

    /**
     * @param $url
     * @return string|null
     */
    protected function getZipPath($url)
    {
        $zip_name = __DIR__ . '/../../../var/brizy-latest.zip';
        $resource = fopen($zip_name, 'w');
        if ($resource === false) {
            $this->errors['error']['zip'][] = 'Can\'t create brizy-latest.zip';
            return null;
        }

        $stream = Stream::factory($resource);
        $client = HttpUtils::getHttpClient();

        $response = $client->get(
            $url,
            ['save_to' => $stream]
        );
        if ($response->getStatusCode() != 200) {
            $this->errors['error']['zip'][] = 'Zip was not downloaded';
            return null;
        }

        $this->postExecuteRemove($zip_name);

        return $zip_name;
    }

    protected function postExecuteRemove($zip_name)
    {
        register_shutdown_function(function ($zip_name) {
            Filesystem::removeFile($zip_name, false);
        }, $zip_name);
    }

}




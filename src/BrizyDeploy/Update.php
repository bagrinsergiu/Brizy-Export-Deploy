<?php

namespace BrizyDeploy;

use BrizyDeploy\Utils\HttpUtils;
use GuzzleHttp\Stream\Stream;

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

    /**
     * @var string
     */
    private $zip_url;

    /**
     * @var string
     */
    private $var_dir;

    /**
     * Update constructor.
     * @param $zip_url
     */
    public function __construct($zip_url)
    {
        $this->zip_url = $zip_url;
        $this->var_dir = realpath(__DIR__ . '/../../var');
        $this->backup_path = __DIR__ . '/../../var/backup.zip';

        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '256M');
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
    public function execute()
    {
        #backup
        $backup = $this->backup(__DIR__ . '/../../', $this->backup_path);
        if (!$backup) {
            $this->errors['backup'] = 'backup was not created';
            return false;
        }

        $this->postExecuteRemove($this->backup_path);

        try {
            #execute
            $result = $this->install($this->getZipPath());
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

    /**
     * @param $zip_path
     * @return bool
     */
    protected function install($zip_path)
    {
        $zip = zip_open($zip_path);
        if (!is_resource($zip)) {
            $this->errors['error']['zip'][] = 'Invalid zip';
            return false;
        }

        $existFiles = $this->getFilesByPath(realpath(__DIR__ . '/../../'));
        $existFiles = $existFiles['files'];

        $result = true;
        while ($zip_entry = zip_read($zip)) {
            $name = zip_entry_name($zip_entry);
            if (!preg_match("/\/$/", $name)) {
                $asset_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                $name = realpath(__DIR__ . '/../../') . '/' . str_replace('brizy/', '', $name);

                if ($key = array_search($name, $existFiles)) {
                    unset($existFiles[$key]);
                }

                $dirname = dirname($name);

                if (!is_dir($dirname)) {
                    mkdir($dirname, 0755, true);
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

        var_dump($existFiles);
        return $result;
    }

    protected function readDir($dir)
    {
        $result = [];
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $result[] = $entry;
                }
            }
            closedir($handle);
        }

        return $result;
    }

    protected function getFilesByPath($root)
    {
        $files = array('files' => array(), 'dirs' => array());
        $directories = array();
        $last_letter = $root[strlen($root) - 1];
        $root = ($last_letter == '\\' || $last_letter == '/') ? $root : $root . DIRECTORY_SEPARATOR;

        $directories[] = $root;

        while (sizeof($directories)) {
            $dir = array_pop($directories);
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $file = $dir . $file;
                    if (is_dir($file)) {
                        $directory_path = $file . DIRECTORY_SEPARATOR;
                        array_push($directories, $directory_path);
                        $files['dirs'][] = $directory_path;
                    } elseif (is_file($file)) {
                        $files['files'][] = $file;
                    }
                }
                closedir($handle);
            }
        }

        return $files;
    }

    /**
     * @param $source
     * @param $destination
     * @return bool
     */
    protected function backup($source, $destination)
    {
        if (file_exists($source) === true) {
            $zip = new \ZipArchive();
            if ($zip->open($destination, \ZIPARCHIVE::CREATE) === true) {
                $source = realpath($source);
                if (is_dir($source) === true) {
                    $iterator = new \RecursiveDirectoryIterator($source);
                    $iterator->setFlags(\RecursiveDirectoryIterator::SKIP_DOTS);
                    $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($files as $file) {
                        $file = realpath($file);

                        #exclude var dir
                        if (strpos($file, $this->var_dir) !== false) {
                            continue;
                        }

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

        return false;
    }

    /**
     * @return string|null
     */
    protected function getZipPath()
    {
        $zip_name = __DIR__ . '/../../var/brizy-latest.zip';
        $resource = fopen($zip_name, 'w');
        if ($resource === false) {
            $this->errors['error']['zip'][] = 'Can\'t create brizy-latest.zip';
            return null;
        }

        $stream = Stream::factory($resource);
        $client = HttpUtils::getHttpClient();

        $response = $client->get(
            $this->zip_url,
            ['save_to' => $stream]
        );
        if ($response->getStatusCode() != 200) {
            $this->errors['error']['zip'][] = 'Zip was not downloaded';
            return null;
        }

        $this->postExecuteRemove($zip_name);

        return $zip_name;
    }

    /**
     * @param $zip_name
     */
    protected function postExecuteRemove($zip_name)
    {
        register_shutdown_function(function ($zip_name) {
            @unlink($zip_name);
        }, $zip_name);
    }
}




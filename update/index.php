<?php

class Update
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
        $this->backup_path = __DIR__ . '/../var/backup.zip';
    }

    public function execute()
    {
        #backup
        $backup = $this->backup(__DIR__ . '/../', $this->backup_path);
        if (!$backup) {
            echo 'backup was not created';
            exit;
        }

        #download
        $zip_path = $this->getZip();
        if ($zip_path === false) {
            echo 'zip was not downloaded';
            exit;
        }

        #simulate
        $result = $this->rewrite($zip_path, true);
        if (!$result) {
            var_dump($this->errors);
            exit;
        }

        try {
            #execute
            $this->rewrite($zip_path);
            if (!$result) {
                #restore from backup
                $this->rewrite($this->backup_path);
                var_dump($this->errors);
                exit;
            }
        } catch (\Exception $e) {
            #restore from backup
            $this->rewrite($this->backup_path);
        }
    }

    public function rewrite($zip_path, $simulate = false)
    {
        $zip = zip_open($zip_path);
        if (!is_resource($zip)) {
            $this->errors['error']['zip'] = 'Invalid zip';
            return false;
        }

        $result = true;
        while ($zip_entry = zip_read($zip)) {
            $name = zip_entry_name($zip_entry);
            if (!preg_match("/\/$/", $name)) {
                $asset_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                $name = __DIR__ . '/../' . str_replace('brizy/', '', $name);

                if ($simulate) {
                    if (!is_writable($name)) {
                        $this->errors['error']['permissions'][] = $name;
                        $result = false;
                    }
                } else {
                    $bytes = file_put_contents($name, $asset_content);
                    if ($bytes === false) {
                        $this->errors['error']['files'][] = $name;
                        $result = false;
                    }
                }
            }
        }

        zip_close($zip);

        return $result;
    }

    public function getZip()
    {
        $binary = $this->_downloadCurl('https://s3.amazonaws.com/bitblox-develop/brizy.zip');
        $path = __DIR__ . '/../var/brizy-latest.zip';
        $bytes = file_put_contents($path, $binary);
        if ($bytes === false) {
            return false;
        }

        return $path;
    }

    public function backup($source, $destination)
    {
        if (extension_loaded('zip') === true) {
            if (file_exists($source) === true) {
                $zip = new ZipArchive();
                if ($zip->open($destination, ZIPARCHIVE::CREATE) === true) {
                    $source = realpath($source);
                    if (is_dir($source) === true) {
                        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
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
     * Download file via curl.
     *
     * @param string $url URL to file
     * @return string|false
     */
    protected function _downloadCurl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/../app/certificates/ca-bundle.crt');
        $update = curl_exec($curl);
        $error = false;
        if (curl_error($curl)) {
            $error = true;
        }
        curl_close($curl);
        if ($error === true) {
            return false;
        }
        return $update;
    }
}

$update = new Update();
$update->execute();


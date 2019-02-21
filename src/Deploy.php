<?php

class Deploy
{
    /**
     * @var array
     */
    protected $errors = array();

    protected $is_succeeded = true;

    /**
     * @todo create param $project_hash_id
     */
    public function execute()
    {
        $zip = zip_open($this->getZipPath());
        if (!is_resource($zip)) {
            $this->is_succeeded = false;
        }

        while ($zip_entry = zip_read($zip)) {
            $name = zip_entry_name($zip_entry);
            if (!preg_match("/\/$/", $name)) {
                $asset_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                $bytes = file_put_contents(__DIR__ . '/../' . $name, $asset_content);
                if ($bytes === false) {
                    $this->errors[] = $name;
                }
            }
        }

        zip_close($zip);

        if (count($this->errors) > 0) {
            $this->is_succeeded = false;
        }
    }

    protected function getZipPath()
    {
        return '/home/andrei/Desktop/dbcdf348d69a85ff0b5a9b002e360fa5/cache.zip';
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function isSucceeded()
    {
        return $this->is_succeeded;
    }
}
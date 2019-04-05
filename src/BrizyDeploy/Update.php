<?php

namespace BrizyDeploy;

class Update extends BaseDeploy
{

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
        $this->var_dir = realpath(__DIR__ . '/../../var');

        parent::__construct($zip_url);
    }

    public function execute()
    {
        return $this->innerExecute();
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getNormalizedName($name)
    {
        return realpath(__DIR__ . '/../../') . '/' . str_replace('brizy/', 'var/script_latest', $name);
    }

    /**
     * @return string
     */
    protected function generateZipName()
    {
        return __DIR__ . '/../../var/brizy-script-' . time() . '.zip';
    }

    /**
     * @return void
     */
    protected function backup()
    {
        copyDirectory(__DIR__ . '/../../', __DIR__ . '/../../var/script_backup');
    }
}
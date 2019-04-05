<?php

namespace BrizyDeploy;

class Deploy extends BaseDeploy
{
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

        parent::__construct($this->brizy_cloud_url . '/projects/' . $this->project_hash_id . '/export');
    }

    public function execute()
    {
        $is_success = $this->innerExecute();

        $params = [
            'source_current' => __DIR__ . '/../../cache',
            'source_latest' => __DIR__ . '/../../var/cache_latest',
            'source_backup' => __DIR__ . '/../../var/cache_backup',
            'dist' => realpath(__DIR__ . '/../../'),
            'success' => $is_success
        ];

        post_deploy_action($params);

        return $is_success;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getNormalizedName($name)
    {
        return realpath(__DIR__ . '/../../') . '/var/cache_latest/' . $name;
    }

    /**
     * @return string
     */
    protected function generateZipName()
    {
        return __DIR__ . '/../../var/brizy-deploy-' . time() . '.zip';
    }

    /**
     * @return bool
     */
    protected function backup()
    {
        return copyDirectory(__DIR__ . '/../../cache', __DIR__ . '/../../var/cache_backup');
    }
}
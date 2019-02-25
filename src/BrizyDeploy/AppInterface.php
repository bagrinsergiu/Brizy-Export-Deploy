<?php

namespace BrizyDeploy;

interface AppInterface
{
    /**
     * @return $this
     */
    public function saveConfig();

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @return bool
     */
    public function isInstalled();

    /**
     * @param $is_installed
     * @return $this
     */
    public function setIsInstalled($is_installed);

    /**
     * @return string
     */
    public function getBrizyCloudUrl();

    /**
     * @param $brizy_cloud_url
     * @return mixed
     */
    public function setBrizyCloudUrl($brizy_cloud_url);

    /**
     * @return string
     */
    public function getProjectHashId();

    /**
     * @param $project_hash_id
     * @return mixed
     */
    public function setProjectHashId($project_hash_id);
}
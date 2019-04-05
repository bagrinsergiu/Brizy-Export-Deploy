<?php

class AppKernel
{
    /**
     * @var string
     */
    protected $config_path;

    /**
     * @var string
     */
    protected $config_path_dist;

    /**
     * @var array
     */
    protected $config;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->config_path = __DIR__ . '/../var/config.json';
        $this->config_path_dist = __DIR__ . '/../app/config/config.json.dist';
        $this->config = $this->toArrayConfig();

        ini_set('max_execution_time', 60);

        @mkdir(__DIR__ . '/../cache', 0755);
    }

    /**
     * @return array
     */
    protected function toArrayConfig()
    {
        $config = @file_get_contents($this->config_path);
        if (!$config) {
            return [];
        }
        $config = json_decode($config, true);
        if (!$config) {
            return [];
        }

        return $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return $this
     */
    public function saveConfig()
    {
        file_put_contents($this->config_path, json_encode($this->config));

        return $this;
    }

    public function init()
    {
        if (!$this->isInit()) {
            copy($this->config_path_dist, $this->config_path);
            $this->config = $this->toArrayConfig();
        }
    }

    public function isInit()
    {
        return is_file($this->config_path);
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        if (isset($this->config['installed'])) {
            return (bool)$this->config['installed'];
        }

        return false;
    }

    /**
     * @param $is_installed
     * @return $this
     */
    public function setIsInstalled($is_installed)
    {
        if (isset($this->config['installed'])) {
            $this->config['installed'] = (bool)$is_installed;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDeployUrl()
    {
        if (isset($this->config['deploy_url'])) {
            return $this->config['deploy_url'];
        }

        return '';
    }

    /**
     * @param $deploy_url
     * @return $this
     */
    public function setDeployUrl($deploy_url)
    {
        if (isset($this->config['deploy_url'])) {
            $this->config['deploy_url'] = $deploy_url;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        if (isset($this->config['app_id'])) {
            return $this->config['app_id'];
        }

        return '';
    }

    /**
     * @param $app_id
     * @return $this
     */
    public function setAppId($app_id)
    {
        if (isset($this->config['app_id'])) {
            $this->config['app_id'] = $app_id;
        }

        return $this;
    }
}
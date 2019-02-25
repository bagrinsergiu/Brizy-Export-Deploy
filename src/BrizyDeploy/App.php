<?php

namespace BrizyDeploy;

use BrizyDeploy\Exception\AppException;

class App implements AppInterface
{
    /**
     * @var string
     */
    protected $config_path;

    /**
     * @var array
     */
    protected $config;

    /**
     * App constructor.
     * @throws AppException
     */
    public function __construct()
    {
        $this->config_path = __DIR__ . '/../../var/config.json';
        $this->config = $this->toArrayConfig();
    }

    /**
     * @return array
     * @throws AppException
     */
    protected function toArrayConfig()
    {
        $config = file_get_contents($this->config_path);
        $config = json_decode($config, true);
        if (!$config) {
            throw new AppException('Invalid config file');
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
}
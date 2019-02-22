<?php

namespace BrizyDeploy;

use BrizyDeploy\Exception\AppException;

class App implements AppInterface
{
    const CONFIG_PATH = __DIR__ . '/../../var/config.json';

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
        $this->config = $this->toArrayConfig();
    }

    /**
     * @return array
     * @throws AppException
     */
    protected function toArrayConfig()
    {
        $config = file_get_contents(self::CONFIG_PATH);
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
        file_put_contents(self::CONFIG_PATH, json_encode($this->config));

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
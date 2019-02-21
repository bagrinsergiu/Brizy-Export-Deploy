<?php

class Config
{
    const CONFIG_PATH = 'var/config.json';

    /**
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->config = $this->toArray();
    }

    /**
     * @return array
     */
    protected function toArray()
    {
        $config = file_get_contents(self::CONFIG_PATH);
        $config = json_decode($config, true);
        if (!$config) {
            return [];
        }

        return $config;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->config;
    }

    /**
     * @return $this
     */
    public function save()
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

    public function setIsInstalled($is_installed)
    {
        if (isset($this->config['installed'])) {
            $this->config['installed'] = (bool)$is_installed;
        }

        return $this;
    }
}
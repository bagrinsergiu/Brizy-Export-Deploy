<?php

namespace BrizyDeploy\Modal;

class Update
{
    /**
     * @var boolean
     */
    protected $execute;

    /**
     * @var string
     */
    protected $version;

    static public function getInstance()
    {
        $update = new Update();

        $config_dist = __DIR__ . '/../../../app/config/config.json.dist';
        $config_dist = json_decode(file_get_contents($config_dist), true);
        if (!$config_dist) {
            $update
                ->setVersion(null)
                ->setExecute(false);
        } else {
            $update
                ->setVersion($config_dist['version'])
                ->setExecute(false);
        }

        return $update;
    }

    /**
     * @return boolean
     */
    public function getExecute()
    {
        return $this->execute;
    }

    /**
     * @param $execute
     * @return $this
     */
    public function setExecute($execute)
    {
        $this->execute = $execute;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->execute,
            $this->version
        ]);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->execute,
            $this->version
            ) = unserialize($serialized);
    }
}
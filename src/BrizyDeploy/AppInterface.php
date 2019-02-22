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
}
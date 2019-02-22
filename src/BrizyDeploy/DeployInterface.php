<?php

namespace BrizyDeploy;

interface DeployInterface
{
    /**
     * @return void
     */
    public function execute();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return bool
     */
    public function isSucceeded();
}
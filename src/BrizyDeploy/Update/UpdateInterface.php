<?php

namespace BrizyDeploy\Update;

interface UpdateInterface
{
    public function execute();

    public function getErrors();
}
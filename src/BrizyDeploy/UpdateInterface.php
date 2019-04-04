<?php

namespace BrizyDeploy;

interface UpdateInterface
{
    public function execute();

    public function getErrors();
}
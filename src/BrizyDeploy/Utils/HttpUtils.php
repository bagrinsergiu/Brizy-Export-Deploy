<?php

namespace BrizyDeploy\Utils;

use Symfony\Component\HttpFoundation\Request;

class HttpUtils
{
    static public function getBaseUrl(Request $request)
    {
        var_dump($request->getBaseUrl());
    }
}